<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationService
{
    public function sendAfterRegistration(User $user): void
    {
        SendEmailVerificationJob::dispatch($user);
    }

    public function resendForRole(string $email, UserRole $role): array
    {
        $user = User::query()
            ->where('email', $email)
            ->where('role', $role)
            ->first();

        if (! $user) {
            return ['status' => 'sent'];
        }

        return $this->resendForUser($user);
    }

    public function resendForUser(User $user, bool $respectThrottle = true): array
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

        SendEmailVerificationJob::dispatch($user);

        return ['status' => 'sent'];
    }

    public function resendAfterBlockedLogin(User $user): void
    {
        $this->resendForUser($user);
    }

    public function verify(string|int $userId, string $hash, bool $hasValidSignature): array
    {
        if (! $hasValidSignature) {
            return ['status' => 'invalid'];
        }

        $user = User::query()->find($userId);

        if (! $user) {
            return ['status' => 'invalid'];
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return ['status' => 'invalid'];
        }

        if ($user->hasVerifiedEmail()) {
            return ['status' => 'already_verified'];
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return ['status' => 'verified'];
    }

    protected function throttleKey(User $user): string
    {
        return sprintf('email-verification:%s', $user->getKey());
    }

    protected function resendCooldownSeconds(): int
    {
        return (int) config('auth.verification.resend_throttle', 60);
    }
}
