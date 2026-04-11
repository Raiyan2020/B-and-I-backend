<?php

namespace App\Services\Auth;

use App\Jobs\SendEmailOtpNotificationJob;
use App\Models\AuthUpdate;
use App\Models\User;
use App\Services\Devices\DeviceService;
use Illuminate\Support\Facades\Hash;

class EmailChangeService
{
    private const TYPE_EMAIL = 'email';
    private const SUB_TYPE_OLD_EMAIL = 'old_email';
    private const SUB_TYPE_NEW_EMAIL = 'new_email';

    public function __construct(
        private readonly DeviceService $deviceService,
    ) {}

    public function requestCurrentEmailOtp(User $user, string $currentPassword): array
    {
        if (! Hash::check($currentPassword, $user->password)) {
            return ['status' => 'current_password_invalid'];
        }

        $this->resetEmailChangeState($user);

        $authUpdate = $this->upsertEmailUpdate(
            user: $user,
            subType: self::SUB_TYPE_OLD_EMAIL,
            attribute: $user->email,
        );

        $this->sendOtpMail(
            email: $user->email,
            otp: $authUpdate->code,
            section: 'change_email_current',
            userName: $user->name ?: __('mail.change_email_current.user_fallback'),
            locale: $user->preferredLocale(),
        );

        return ['status' => 'sent'];
    }

    public function verifyCurrentEmailOtp(User $user, string $otp): array
    {
        $authUpdate = $this->latestPendingEmailUpdate($user, self::SUB_TYPE_OLD_EMAIL, $user->email);

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

    public function requestNewEmailOtp(User $user, string $newEmail): array
    {
        if (! $this->hasVerifiedCurrentEmail($user)) {
            return ['status' => 'current_email_verification_required'];
        }

        if (strcasecmp($user->email ?? '', $newEmail) === 0) {
            return ['status' => 'same_email'];
        }

        $authUpdate = $this->upsertEmailUpdate(
            user: $user,
            subType: self::SUB_TYPE_NEW_EMAIL,
            attribute: $newEmail,
        );

        $this->sendOtpMail(
            email: $newEmail,
            otp: $authUpdate->code,
            section: 'change_email_new',
            userName: $user->name ?: __('mail.change_email_new.user_fallback'),
            locale: $user->preferredLocale(),
        );

        return ['status' => 'sent'];
    }

    private function upsertEmailUpdate(User $user, string $subType, string $attribute): AuthUpdate
    {
        return $user->authUpdates()->updateOrCreate(
            [
                'type' => self::TYPE_EMAIL,
                'sub_type' => $subType,
            ],
            [
                'attribute' => $attribute,
                'code' => $this->generateOtp(),
                'code_expires_at' => $this->otpExpiresAt(),
                'verified_at' => null,
            ],
        );
    }

    public function verifyNewEmailOtp(User $user, string $newEmail, string $otp): array
    {
        if (! $this->hasVerifiedCurrentEmail($user)) {
            return ['status' => 'current_email_verification_required'];
        }

        $newEmailUpdate = $this->latestPendingEmailUpdate($user, self::SUB_TYPE_NEW_EMAIL, $newEmail);

        if (! $newEmailUpdate || ! $this->otpMatches($newEmailUpdate->code, $newEmailUpdate->code_expires_at, $otp)) {
            return ['status' => 'invalid'];
        }

        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => now(),
        ])->save();

        $this->resetEmailChangeState($user);

        $user->tokens()->delete();
        $this->deviceService->forgetAllUserDevices($user);

        return ['status' => 'changed_logged_out_everywhere'];
    }

    private function resetEmailChangeState(User $user): void
    {
        $user->authUpdates()
            ->where('type', self::TYPE_EMAIL)
            ->delete();
    }

    private function hasVerifiedCurrentEmail(User $user): bool
    {
        return $user->authUpdates()
            ->where('type', self::TYPE_EMAIL)
            ->where('sub_type', self::SUB_TYPE_OLD_EMAIL)
            ->where('attribute', $user->email)
            ->whereNull('code')
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes($this->verificationWindowMinutes()))
            ->exists();
    }

    private function latestPendingEmailUpdate(User $user, string $subType, string $attribute): ?AuthUpdate
    {
        return $user->authUpdates()
            ->where('type', self::TYPE_EMAIL)
            ->where('sub_type', $subType)
            ->where('attribute', $attribute)
            ->whereNotNull('code')
            ->latest('id')
            ->first();
    }

    private function otpMatches(mixed $storedOtp, mixed $expiresAt, string $providedOtp): bool
    {
        return filled($storedOtp)
            && $expiresAt !== null
            && ! $expiresAt->isPast()
            && hash_equals((string) $storedOtp, $providedOtp);
    }

    private function sendOtpMail(string $email, string $otp, string $section, string $userName, string $locale): void
    {
        SendEmailOtpNotificationJob::dispatch($email, $otp, $section, $userName, $locale);
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
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
