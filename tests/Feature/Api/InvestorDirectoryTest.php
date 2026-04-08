<?php

namespace Tests\Feature\Api;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use App\Models\PreferredSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestorDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_active_investors_with_pagination(): void
    {
        User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password',
            'is_active' => true,
            'is_blocked' => false,
            'investor_type' => InvestorType::Angel,
            'available_capital' => 100000,
            'investor_experience' => InvestorExperience::Intermediate,
        ]);

        User::factory()->create([
            'role' => UserRole::Advertiser,
            'password' => 'password',
        ]);

        User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password',
            'is_blocked' => true,
        ]);

        $response = $this->getJson('/api/v1/general/investors?per_page=10');

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonCount(1, 'data.investors')
            ->assertJsonPath('data.investors.0.investor_type.value', 'angel')
            ->assertJsonPath('data.investors.0.available_capital', 100000);
    }

    public function test_filters_by_type_capital_experience_and_sector(): void
    {
        $sector = PreferredSector::query()->create([
            'name' => ['ar' => 'مطاعم', 'en' => 'Restaurants'],
            'status' => true,
        ]);

        User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password',
            'investor_type' => InvestorType::Company,
            'available_capital' => 500000,
            'investor_experience' => InvestorExperience::Expert,
            'preferred_sector_id' => $sector->id,
        ]);

        User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password',
            'investor_type' => InvestorType::Angel,
            'available_capital' => 10000,
            'investor_experience' => InvestorExperience::Beginner,
        ]);

        $response = $this->getJson('/api/v1/general/investors?'.http_build_query([
            'investor_type' => 'company',
            'min_capital' => 100000,
            'max_capital' => 600000,
            'investor_experience' => 'expert',
            'preferred_sector_id' => $sector->id,
        ]));

        $response->assertOk();
        $response->assertJsonPath('data.pagination.total', 1);
        $response->assertJsonPath('data.investors.0.focus_sector.id', $sector->id);
    }

    public function test_rejects_max_capital_below_min_capital(): void
    {
        $response = $this->getJson('/api/v1/general/investors?min_capital=100&max_capital=50');

        $response->assertStatus(422);
    }
}
