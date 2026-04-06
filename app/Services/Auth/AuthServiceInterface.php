<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Models\User;

interface AuthServiceInterface
{
    /**
     * @return array{user: User, token: string}
     */
    public function registerInvestor(RegisterInvestorDTO $dto): array;

    /**
     * @return array{user: User, token: string}
     */
    public function registerAdvertiser(RegisterAdvertiserDTO $dto): array;

    /**
     * @return array{user: User, token: string}
     */
    public function login(LoginDTO $dto): array;

    public function logout(User $user): void;

    public function resendVerification(User $user): void;
}
