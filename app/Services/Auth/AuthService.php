<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function registerInvestor(RegisterInvestorDTO $dto): array
    {
        $user = User::create([
            'role' => 'investor',
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
            'investor_type' => $dto->investor_type,
            'investor_sector' => $dto->investor_sector,
            'investor_capital' => $dto->investor_capital,
            'investment_count' => $dto->investment_count,
            'investor_experience' => $dto->investor_experience,
        ]);

        $token = $user->createToken('investor')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function registerAdvertiser(RegisterAdvertiserDTO $dto): array
    {
        // file storage and other logic should be implemented here
        $user = User::create([
            'role' => 'advertiser',
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
            'company_name' => $dto->company_name,
            'license_number' => $dto->license_number,
        ]);

        $token = $user->createToken('advertiser')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(LoginDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();
        if (! $user || $user->phone !== $dto->phone) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid credentials.');
        }

        $user->tokens()->delete();
        $token = $user->createToken($user->role)->plainTextToken;

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
