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

class InvestorOpportunityDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_investor_can_list_purchased_seat_opportunities_without_sent_interest(): void
    {
        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);
        $otherInvestor = User::factory()->create(['role' => UserRole::Investor]);

        $eligibleOpportunity = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Purchased Seat Only',
        ]);
        $interestOpportunity = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Sent Interest',
        ]);

        InvestmentSeat::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $eligibleOpportunity->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);
        $seat = InvestmentSeat::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $interestOpportunity->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);
        InvestmentSeat::query()->create([
            'user_id' => $otherInvestor->id,
            'opportunity_id' => $eligibleOpportunity->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);

        InterestRequest::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $interestOpportunity->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($investor);

        $response = $this->getJson('/api/v1/investor/opportunities/purchased-seats?per_page=10');

        $response->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonCount(1, 'data.opportunities')
            ->assertJsonPath('data.opportunities.0.company_name', 'Purchased Seat Only')
            ->assertJsonPath('data.opportunities.0.has_seat', true)
            ->assertJsonPath('data.opportunities.0.has_submitted_interest', false)
            ->assertJsonPath('data.opportunities.0.file_access.key', 'open');
    }

    public function test_investor_can_list_sent_interest_opportunities(): void
    {
        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);

        $opportunity = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Interested Opportunity',
        ]);

        $seat = InvestmentSeat::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $opportunity->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);

        InterestRequest::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $opportunity->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($investor);

        $response = $this->getJson('/api/v1/investor/opportunities/sent-interests');

        $response->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.opportunities.0.company_name', 'Interested Opportunity')
            ->assertJsonPath('data.opportunities.0.has_seat', true)
            ->assertJsonPath('data.opportunities.0.has_submitted_interest', true);
    }

    public function test_investor_can_list_current_requests_that_are_reserved_for_him(): void
    {
        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);
        $otherInvestor = User::factory()->create(['role' => UserRole::Investor]);

        $reservedForInvestor = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
            'investor_id' => $investor->id,
            'company_name' => 'Current Request',
        ]);
        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Completed,
            'investor_id' => $investor->id,
            'company_name' => 'Completed Request',
        ]);
        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
            'investor_id' => $otherInvestor->id,
            'company_name' => 'Other Investor Request',
        ]);

        $seat = InvestmentSeat::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $reservedForInvestor->id,
            'price_paid' => 50,
            'purchased_at' => now(),
        ]);

        InterestRequest::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $reservedForInvestor->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($investor);

        $response = $this->getJson('/api/v1/investor/opportunities/current-requests');

        $response->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.opportunities.0.company_name', 'Current Request')
            ->assertJsonPath('data.opportunities.0.status.value', OpportunityStatus::Reserved->value)
            ->assertJsonPath('data.opportunities.0.has_submitted_interest', true);
    }

    public function test_advertiser_cannot_access_investor_dashboard_endpoints(): void
    {
        $advertiser = User::factory()->create(['role' => UserRole::Advertiser]);

        Sanctum::actingAs($advertiser);

        $this->getJson('/api/v1/investor/opportunities/purchased-seats')
            ->assertStatus(403)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.investor_only_action'));

        $this->getJson('/api/v1/investor/opportunities/sent-interests')
            ->assertStatus(403);

        $this->getJson('/api/v1/investor/opportunities/current-requests')
            ->assertStatus(403);
    }
}
