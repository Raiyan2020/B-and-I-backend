<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Enums\UserRole;
use App\Models\User;
use Exception;

class AuthService implements AuthServiceInterface
{
    public function registerInvestor(RegisterInvestorDTO $dto): array
    {
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
        ]);

        $token = $user->createToken(UserRole::Investor->value)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function registerAdvertiser(RegisterAdvertiserDTO $dto): array
    {
        // file storage and other logic should be implemented here
        $user = User::create([
            'role' => UserRole::Advertiser,
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'country_code' => $dto->country_code,
            'password' => $dto->password,
            'company_license' => $dto->company_license,
        ]);

        $token = $user->createToken(UserRole::Advertiser->value)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(LoginDTO $dto): array
    {
        $user = User::where([
            'email' => $dto->email,
            'country_code' => $dto->country_code,
            'phone' => $dto->phone,
        ])->first();
        if (! $user) {
            throw new Exception(__('apis.invalid_credentials'));
        }

        if (!isset($user->email_verified_at)) {
            throw new Exception(__('apis.email_not_verified'));
        }

        $user->tokens()->delete();
        $token = $user->createToken($user->role->value)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function resendVerification(User $user): void
    {
        if ($user->email_verified_at) {
            throw new \InvalidArgumentException(__('apis.already_verified'));
        }

        $user->sendEmailVerificationNotification();
    }
}
