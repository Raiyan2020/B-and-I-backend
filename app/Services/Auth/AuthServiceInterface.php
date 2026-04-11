<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Models\User;

interface AuthServiceInterface
{
    /**
     * @return User
     */
    public function registerInvestor(RegisterInvestorDTO $dto): User;

    /**
     * @return User
     */
    public function registerAdvertiser(RegisterAdvertiserDTO $dto): User;

    /**
     * @return array{status: string, user?: User, token?: string}
     */
    public function login(LoginDTO $dto): array;

    public function issueTokenForUser(User $user, ?string $deviceToken = null, ?string $deviceType = null): string;

    public function logout(User $user, ?string $deviceToken = null): void;

    /**
     * @param  array<string, mixed>  $data
     * @return array{user: User, email_verification_sent: bool}
     */
    public function updateProfile(User $user, array $data): array;

    /**
     * @return array{status: string}
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array;

    /**
     * @return array{status: string}
     */
    public function requestForgotPasswordOtp(string $email): array;

    /**
     * @return array{status: string}
     */
    public function verifyForgotPasswordOtp(string $email, string $otp): array;

    /**
     * @return array{status: string}
     */
    public function resetForgottenPassword(string $email, string $newPassword): array;
}
