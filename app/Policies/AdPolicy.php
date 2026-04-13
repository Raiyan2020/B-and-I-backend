<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Opportunity;
use App\Models\User;

class AdPolicy
{
    public function update(User $user, Opportunity $ad): bool
    {
        return $user->role === UserRole::Advertiser
            && $ad->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Advertiser;
    }
}
