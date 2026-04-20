<?php

namespace Tests\Feature\Api;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyOpportunityDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_list_sent_interest_opportunities_as_buyer(): void
    {
        $category = Category::factory()->create(['status' => true]);
        $company = User::factory()->create(['role' => UserRole::Advertiser]);

        $interestedOpportunity = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Interested Opportunity',
        ]);
        $otherOpportunity = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'No Interest Opportunity',
        ]);

        $seat = InvestmentSeat::query()->create([
            'user_id' => $company->id,
            'opportunity_id' => $interestedOpportunity->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);
        InvestmentSeat::query()->create([
            'user_id' => $company->id,
            'opportunity_id' => $otherOpportunity->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);

        InterestRequest::query()->create([
            'user_id' => $company->id,
            'opportunity_id' => $interestedOpportunity->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($company);

        $response = $this->getJson('/api/v1/company/opportunities/sent-interests');

        $response->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.opportunities.0.company_name', 'Interested Opportunity')
            ->assertJsonPath('data.opportunities.0.has_seat', true)
            ->assertJsonPath('data.opportunities.0.has_submitted_interest', true)
            ->assertJsonPath('data.opportunities.0.file_access.key', 'open');
    }

    public function test_company_can_list_current_requests_reserved_for_it(): void
    {
        $category = Category::factory()->create(['status' => true]);
        $company = User::factory()->create(['role' => UserRole::Advertiser]);
        $otherCompany = User::factory()->create(['role' => UserRole::Advertiser]);

        $reservedForCompany = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
            'investor_id' => $company->id,
            'company_name' => 'Current Request',
        ]);
        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Completed,
            'investor_id' => $company->id,
            'company_name' => 'Completed Request',
        ]);
        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
            'investor_id' => $otherCompany->id,
            'company_name' => 'Other Company Request',
        ]);

        $seat = InvestmentSeat::query()->create([
            'user_id' => $company->id,
            'opportunity_id' => $reservedForCompany->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);

        InterestRequest::query()->create([
            'user_id' => $company->id,
            'opportunity_id' => $reservedForCompany->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($company);

        $response = $this->getJson('/api/v1/company/opportunities/current-requests');

        $response->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.opportunities.0.company_name', 'Current Request')
            ->assertJsonPath('data.opportunities.0.status.value', OpportunityStatus::Reserved->value)
            ->assertJsonPath('data.opportunities.0.has_submitted_interest', true);
    }

    public function test_investor_cannot_access_company_sent_interests_or_current_requests(): void
    {
        $investor = User::factory()->create(['role' => UserRole::Investor]);

        Sanctum::actingAs($investor);

        $this->getJson('/api/v1/company/opportunities/sent-interests')
            ->assertStatus(403)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.have_no_permission'));

        $this->getJson('/api/v1/company/opportunities/current-requests')
            ->assertStatus(403)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.have_no_permission'));
    }
}
