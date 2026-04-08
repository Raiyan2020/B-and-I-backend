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

    public function logout(User $user): void;
}
