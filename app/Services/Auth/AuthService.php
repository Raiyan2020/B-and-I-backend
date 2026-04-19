<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Enums\UserRole;
use App\Jobs\SendEmailOtpNotificationJob;
use App\Models\AuthUpdate;
use App\Models\User;
use App\Services\ProfileUpdateRequestService;
use App\Services\Devices\DeviceService;
use App\Services\NotificationCycleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    private const AUTH_UPDATE_TYPE_PASSWORD = 'password';
    private const AUTH_UPDATE_SUB_TYPE_FORGOT_PASSWORD = 'forgot_password';

    public function __construct(
        private readonly EmailVerificationService $emailVerificationService,
        private readonly DeviceService $deviceService,
        private readonly ProfileUpdateRequestService $profileUpdateRequestService,
        private readonly NotificationCycleService $notificationCycleService,
        private readonly AccountAccessService $accountAccessService,
    ) {}

    public function registerInvestor(RegisterInvestorDTO $dto): User
    {
        try{
            return DB::transaction(function () use ($dto) {
                $user = User::create([
                    'role' => UserRole::Investor,
                    'first_name' => $dto->first_name,
                    'last_name' => $dto->last_name,
                    'email' => $dto->email,
                    'phone' => $dto->phone,
                    'country_code' => $dto->country_code,
                    'password' => $dto->password,
                    'investor_type' => $dto->investor_type,
                    'preferred_sector_id' => $dto->preferred_sector_id,
                    'category_id' => $dto->category_id,
                    'capital' => $dto->capital,
                    'available_capital' => $dto->available_capital,
                    'previous_investments_count' => $dto->previous_investments_count,
                    'investor_experience' => $dto->investment_experience,
                    'experience_level' => $dto->experience_level,
                    'lang' => app()->getLocale(),
                ]);

                DB::afterCommit(function () use ($user): void {
                    $this->emailVerificationService->sendAfterRegistration($user);
                    $this->notificationCycleService->adminNewUserRegistered($user);
                });

                return $this->loadUserWithRelations($user);
            });
        }
        catch (\Exception $e) {
            throw new \Exception(__('auth.registration_failed') . ': ' . $e->getMessage());
        }
    }

    public function registerAdvertiser(RegisterAdvertiserDTO $dto): User
    {

        $user = User::create([
            'role' => UserRole::Advertiser,
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'country_code' => $dto->country_code,
            'password' => $dto->password,
            'company_license' => $dto->company_license,
            'lang' => app()->getLocale(),
        ]);

        DB::afterCommit(function () use ($user): void {
            $this->emailVerificationService->sendAfterRegistration($user);
            $this->notificationCycleService->adminNewUserRegistered($user);
        });

        return $this->loadUserWithRelations($user);
    }

    public function login(LoginDTO $dto): array
    {
        $query = User::query()->with(['preferredSector', 'category']);

        if ($dto->email !== null) {
            $query->where('email', $dto->email);
        } else {
            $query->where('phone', $dto->phone)
                ->where('country_code', $dto->country_code);
        }

        $user = $query->first();

        if (! $user) {
            return ['status' => 'invalid_credentials'];
        }

        if (! Hash::check($dto->password, $user->password)) {
            return ['status' => 'invalid_credentials'];
        }

        if ($user->is_blocked) {
            $this->accountAccessService->revokeUserAccess($user);

            return ['status' => 'blocked'];
        }

        if (! $user->is_active) {
            $this->accountAccessService->revokeUserAccess($user);

            return ['status' => 'inactive'];
        }

        if (! $user->hasVerifiedEmail()) {
            $this->emailVerificationService->resendAfterBlockedLogin($user, $dto->password);

            return ['status' => 'email_unverified','data' => ['email' => $user->email,'password' => $dto->password]];
        }

        return [
            'status' => 'authenticated',
            'user' => $user,
            'token' => $this->issueTokenForUser($user, $dto->device_token, $dto->device_type),
        ];
    }

    public function issueTokenForUser(User $user, ?string $deviceToken = null, ?string $deviceType = null): string
    {
        $tokenName = $deviceToken
            ? $user->role->value.'-'.substr(hash('sha256', $deviceToken), 0, 24)
            : $user->role->value.'-'.Str::uuid();

        $user->tokens()->where('name', $tokenName)->delete();
        $token = $user->createToken($tokenName)->plainTextToken;

        if ($deviceToken) {
            $this->deviceService->syncUserDevice(
                $user,
                $deviceToken,
                $deviceType,
                $user->lang ?: app()->getLocale(),
            );
        }

        return $token;
    }

    public function logout(User $user, ?string $deviceToken = null): void
    {
        $user->currentAccessToken()?->delete();
        $this->deviceService->forgetUserDevice($user, $deviceToken);
    }

    public function updateProfile(User $user, array $data): array
    {
        $result = $this->profileUpdateRequestService->submit($user, $data);
        $user = $this->loadUserWithRelations($user);

        return [
            'status' => $result['status'],
            'user' => $user,
            'email_verification_sent' => false,
        ];
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        if (! Hash::check($currentPassword, $user->password)) {
            return ['status' => 'current_password_invalid'];
        }

        $user->update([
            'password' => $newPassword,
        ]);

        $user->tokens()->delete();
        $this->deviceService->forgetAllUserDevices($user);

        return ['status' => 'updated_logged_out_everywhere'];
    }

    public function requestForgotPasswordOtp(string $email): array
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            return ['status' => 'account_not_found'];
        }

        $authUpdate = $user->authUpdates()->updateOrCreate(
            [
                'type' => self::AUTH_UPDATE_TYPE_PASSWORD,
                'sub_type' => self::AUTH_UPDATE_SUB_TYPE_FORGOT_PASSWORD,
            ],
            [
                'attribute' => $user->email,
                'code' => $this->generateOtp(),
                'code_expires_at' => $this->otpExpiresAt(),
                'verified_at' => null,
            ],
        );

        $this->sendOtpMail(
            email: $user->email,
            otp: $authUpdate->code,
            section: 'forgot_password',
            userName: $user->name ?: __('mail.forgot_password.user_fallback'),
            locale: $user->preferredLocale(),
        );

        return ['status' => 'sent'];
    }

    public function verifyForgotPasswordOtp(string $email, string $otp): array
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            return ['status' => 'invalid'];
        }

        $authUpdate = $this->forgotPasswordPendingUpdate($user, $email);

        if (! $authUpdate || ! $this->otpMatches($authUpdate->code, $authUpdate->code_expires_at, $otp)) {
            return ['status' => 'invalid'];
        }

        $authUpdate->update([
            'code' => null,
            'code_expires_at' => null,
            'verified_at' => now(),
        ]);

        return ['status' => 'verified'];
    }

    public function resetForgottenPassword(string $email, string $newPassword): array
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user || ! $this->hasVerifiedForgotPasswordUpdate($user, $email)) {
            return ['status' => 'verification_required'];
        }

        $user->update([
            'password' => $newPassword,
        ]);

        $this->resetForgotPasswordState($user);
        $user->tokens()->delete();
        $this->deviceService->forgetAllUserDevices($user);

        return ['status' => 'reset_successfully'];
    }

    private function loadUserWithRelations(User $user): User
    {
        return User::query()
            ->with(['preferredSector', 'category'])
            ->findOrFail($user->id);
    }

    private function forgotPasswordPendingUpdate(User $user, string $email): ?AuthUpdate
    {
        return $user->authUpdates()
            ->where('type', self::AUTH_UPDATE_TYPE_PASSWORD)
            ->where('sub_type', self::AUTH_UPDATE_SUB_TYPE_FORGOT_PASSWORD)
            ->where('attribute', $email)
            ->whereNotNull('code')
            ->latest('id')
            ->first();
    }

    private function hasVerifiedForgotPasswordUpdate(User $user, string $email): bool
    {
        return $user->authUpdates()
            ->where('type', self::AUTH_UPDATE_TYPE_PASSWORD)
            ->where('sub_type', self::AUTH_UPDATE_SUB_TYPE_FORGOT_PASSWORD)
            ->where('attribute', $email)
            ->whereNull('code')
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes($this->verificationWindowMinutes()))
            ->exists();
    }

    private function resetForgotPasswordState(User $user): void
    {
        $user->authUpdates()
            ->where('type', self::AUTH_UPDATE_TYPE_PASSWORD)
            ->delete();
    }

    private function sendOtpMail(string $email, string $otp, string $section, string $userName, string $locale): void
    {
        SendEmailOtpNotificationJob::dispatch($email, $otp, $section, $userName, $locale);
    }

    private function otpMatches(mixed $storedOtp, mixed $expiresAt, string $providedOtp): bool
    {
        return filled($storedOtp)
            && $expiresAt !== null
            && ! $expiresAt->isPast()
            && hash_equals((string) $storedOtp, $providedOtp);
    }

    private function otpExpiresAt()
    {
        return now()->addMinutes($this->verificationWindowMinutes());
    }

    private function verificationWindowMinutes(): int
    {
        return (int) config('auth.verification.expire', 60);
    }

    private function generateOtp(): string
    {//TODO:: Remove this hardcoded OTP and use a random one instead, this is just for testing purposes
        return 123456 ??str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
