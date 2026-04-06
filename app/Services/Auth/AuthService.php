<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Enums\UserRole;
use App\Models\User;

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
        $user = User::where('email', $dto->email)->first();
        if (! $user || $user->phone !== $dto->phone) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid credentials.');
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
            throw new \InvalidArgumentException('Already verified');
        }

        $user->sendEmailVerificationNotification();
    }
}
