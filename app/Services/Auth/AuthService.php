<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\Devices\DeviceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly EmailVerificationService $emailVerificationService,
        private readonly DeviceService $deviceService,
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

                DB::afterCommit(fn () => $this->emailVerificationService->sendAfterRegistration($user));

                return $user;
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

        DB::afterCommit(fn () => $this->emailVerificationService->sendAfterRegistration($user));

        return $user;
    }

    public function login(LoginDTO $dto): array
    {
        $query = User::query();

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

        if (! $user->hasVerifiedEmail()) {
            $this->emailVerificationService->resendAfterBlockedLogin($user);

            return ['status' => 'email_unverified'];
        }

        $tokenName = $dto->device_token
            ? $user->role->value.'-'.substr(hash('sha256', $dto->device_token), 0, 24)
            : $user->role->value.'-'.Str::uuid();

        $user->tokens()->where('name', $tokenName)->delete();
        $token = $user->createToken($tokenName)->plainTextToken;

        if ($dto->device_token) {
            $this->deviceService->syncUserDevice(
                $user,
                $dto->device_token,
                $dto->device_type,
                $user->lang ?: app()->getLocale(),
            );
        }

        return ['status' => 'authenticated', 'user' => $user, 'token' => $token];
    }

    public function logout(User $user, ?string $deviceToken = null): void
    {
        $user->currentAccessToken()?->delete();
        $this->deviceService->forgetUserDevice($user, $deviceToken);
    }

    public function updateProfile(User $user, array $data): array
    {
        $emailChanged = array_key_exists('email', $data)
            && $data['email'] !== null
            && $data['email'] !== $user->email;

        if ($user->role !== UserRole::Investor) {
            unset(
                $data['available_capital'],
                $data['preferred_sector_id'],
                $data['category_id'],
                $data['investor_experience'],
                $data['investor_type'],
            );
        } else {
            unset($data['company_license']);

            if (array_key_exists('available_capital', $data) && ! array_key_exists('capital', $data)) {
                $data['capital'] = $data['available_capital'];
            }
        }

        $user->fill($data);

        if ($emailChanged) {
            $user->forceFill([
                'email_verified_at' => null,
            ]);
        }

        $user->save();
        $user->refresh()->loadMissing(['preferredSector', 'category']);

        if ($emailChanged) {
            $this->emailVerificationService->resendForUser($user, false);
        }

        return [
            'user' => $user,
            'email_verification_sent' => $emailChanged,
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

        return ['status' => 'updated'];
    }
}
