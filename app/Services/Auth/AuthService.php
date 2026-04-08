<?php

namespace App\Services\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\DTO\Auth\LoginDTO;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(private readonly EmailVerificationService $emailVerificationService) {}

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

        $user->tokens()->delete();
        $token = $user->createToken($user->role->value)->plainTextToken;

        return ['status' => 'authenticated', 'user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
