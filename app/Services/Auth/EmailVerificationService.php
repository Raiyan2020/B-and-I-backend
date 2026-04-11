<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationService
{
    public function sendAfterRegistration(User $user): void
    {
        $this->refreshOtpForUser($user);
        SendEmailVerificationJob::dispatch($user);
    }

    public function resendForRole(string $email, string $password, UserRole $role): array
    {
        $user = User::query()
            ->where('email', $email)
            ->where('role', $role->value)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ['status' => 'sent'];
        }

        return $this->resendForUser($user, $password);
    }

    public function resendForUser(User $user,string $password , bool $respectThrottle = true): array
    {
        if ($user->hasVerifiedEmail()) {
            return ['status' => 'already_verified'];
        }

        if ($respectThrottle && RateLimiter::tooManyAttempts($this->throttleKey($user), 1)) {
            return [
                'status' => 'throttled',
                'retry_after' => RateLimiter::availableIn($this->throttleKey($user)),
            ];
        }

        if ($respectThrottle) {
            RateLimiter::hit($this->throttleKey($user), $this->resendCooldownSeconds());
        }

        $this->refreshOtpForUser($user);
        SendEmailVerificationJob::dispatch($user);

        return ['status' => 'sent', 'data' => ['email' => $user->email,'password' => $password]];
    }

    public function resendAfterBlockedLogin(User $user, string $password): void
    {
        $this->resendForUser($user, $password);
    }

    public function verifyOtp(string $email, string $password, string $otp): array
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ['status' => 'invalid_credentials'];
        }

        if ($user->hasVerifiedEmail()) {
            return ['status' => 'already_verified', 'user' => $user];
        }

        if (
            blank($user->otp)
            || blank($user->otp_expires_at)
            || ! hash_equals((string) $user->otp, $otp)
            || $user->otp_expires_at->isPast()
        ) {
            return ['status' => 'invalid'];
        }

        $user->forceFill([
            'email_verified_at' => Carbon::now(),
            'otp' => null,
            'otp_expires_at' => null,
        ])->save();

        event(new Verified($user));

        return ['status' => 'verified', 'user' => $user->fresh()];
    }

    protected function throttleKey(User $user): string
    {
        return sprintf('email-verification:%s', $user->getKey());
    }

    protected function resendCooldownSeconds(): int
    {
        return (int) config('auth.verification.resend_throttle', 60);
    }

    protected function refreshOtpForUser(User $user): void
    {
        $user->forceFill([
            'otp' => $this->generateOtp(),
            'otp_expires_at' => now()->addMinutes((int) config('auth.verification.expire', 60)),
        ])->save();
    }

    protected function generateOtp(): string
    {
       // TODO::generate a fixed OTP for testing purposes
        return 123456 ?? str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
