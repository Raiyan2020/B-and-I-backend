<?php

namespace Tests\Feature\Api;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Device;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneralStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_endpoint_returns_expected_counts(): void
    {
        $category = Category::factory()->create(['status' => true]);

        $advertiserOne = User::factory()->create([
            'role' => UserRole::Advertiser,
            'category_id' => $category->id,
        ]);
        $advertiserTwo = User::factory()->create([
            'role' => UserRole::Advertiser,
            'category_id' => $category->id,
        ]);
        $investorOne = User::factory()->create([
            'role' => UserRole::Investor,
            'category_id' => $category->id,
        ]);
        $investorTwo = User::factory()->create([
            'role' => UserRole::Investor,
            'category_id' => $category->id,
        ]);

        Opportunity::factory()->create([
            'user_id' => $advertiserOne->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
        ]);
        Opportunity::factory()->create([
            'user_id' => $advertiserOne->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
        ]);
        Opportunity::factory()->create([
            'user_id' => $advertiserTwo->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::Completed,
        ]);
        Opportunity::factory()->create([
            'user_id' => $advertiserTwo->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::NeedsRevision,
        ]);

        Device::query()->create([
            'user_id' => $advertiserOne->id,
            'token' => 'device-token-1',
            'device_type' => 'web',
            'locale' => 'ar',
        ]);
        Device::query()->create([
            'user_id' => $advertiserOne->id,
            'token' => 'device-token-2',
            'device_type' => 'ios',
            'locale' => 'ar',
        ]);
        Device::query()->create([
            'user_id' => $investorOne->id,
            'token' => 'device-token-3',
            'device_type' => 'android',
            'locale' => 'en',
        ]);
        Device::query()->create([
            'user_id' => null,
            'token' => 'device-token-4',
            'device_type' => 'web',
            'locale' => 'en',
        ]);

        $response = $this->getJson('/api/v1/general/statistics');

        $response->assertOk()
            ->assertJsonPath('data.advertisers_count', 2)
            ->assertJsonPath('data.investors_count', 2)
            ->assertJsonPath('data.projects_count', 4)
            ->assertJsonPath('data.online_users_count', 2)
            ->assertJsonPath('data.successful_deals_count', 1)
            ->assertJsonPath('data.proposed_deals_count', 1);
    }
}
