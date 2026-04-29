<?php

namespace Tests\Feature\Api;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\InvestmentSeat;
use App\Models\Notification;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OpportunityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_create_opportunity_in_pending_status(): void
    {
        Admin::factory()->count(2)->create();

        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $category = Category::factory()->create(['status' => true]);

        Sanctum::actingAs($company);

        $response = $this->postJson('/api/v1/company/opportunities', [
            'goal' => 'request_investment',
            'contact_name' => 'Ahmed Mohamed',
            'contact_phone' => '80808080',
            'contact_email' => 'company@gmail.com',
            'owner_name' => 'Ahmed Mohamed',
            'admin_company_name' => 'Elite Trading Company',
            'license_number' => 'LIC-123456',
            'company_name' => 'Restaurant Opportunity No. 10',
            'category_id' => $category->id,
            'business_age_years' => 3,
            'investment_required' => 65000,
            'business_stage' => 'Operational',
            'sale_percentage' => 20,
            'legal_entity' => 'Limited Liability Company',
            'financial_status' => 'Stable',
            'investment_reason' => 'Expand operations and open two new branches.',
            'full_description' => 'Established restaurant business generating stable profits.',
            'terms_accepted' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.opportunity_number', 'PROJ-'.now()->format('Y').'-001')
            ->assertJsonPath('data.status.key', OpportunityStatus::Pending->value)
            ->assertJsonPath('data.sale_percentage', 20);

        $this->assertDatabaseHas('opportunities', [
            'user_id' => $company->id,
            'status' => OpportunityStatus::Pending->value,
            'company_name' => 'Restaurant Opportunity No. 10',
        ]);

        $createdOpportunity = Opportunity::query()
            ->where('user_id', $company->id)
            ->where('company_name', 'Restaurant Opportunity No. 10')
            ->firstOrFail();

        $adminNotifications = Notification::query()
            ->where('notifiable_type', Admin::class)
            ->where('data->notification_type', 'create_opportunity_for_admin')
            ->get();

        $this->assertCount(2, $adminNotifications);
        $this->assertTrue(
            $adminNotifications->every(
                fn (Notification $notification) => data_get($notification->data, 'model_id') === $createdOpportunity->id
                    && data_get($notification->data, 'owner_id') === $company->id
            )
        );
    }

    public function test_only_published_and_reserved_opportunities_are_visible_publicly(): void
    {
        $category = Category::factory()->create(['status' => true]);

        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Pending,
            'company_name' => 'Pending Company',
        ]);

        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Published Company',
        ]);

        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
            'company_name' => 'Reserved Company',
        ]);

        $response = $this->getJson('/api/v1/general/opportunities');

        $response->assertOk()
            ->assertJsonCount(2, 'data.opportunities')
            ->assertJsonFragment(['company_name' => 'Reserved Company'])
            ->assertJsonFragment(['company_name' => 'Published Company']);
    }

    public function test_company_can_list_own_opportunities_with_pagination(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);

        Opportunity::factory()->count(3)->create([
            'user_id' => $company->id,
            'status' => OpportunityStatus::Pending,
        ]);

        Sanctum::actingAs($company);

        $response = $this->getJson('/api/v1/company/opportunities?per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data.opportunities')
            ->assertJsonPath('data.pagination.current_page', 1)
            ->assertJsonPath('data.pagination.per_page', 2)
            ->assertJsonPath('data.pagination.total', 3)
            ->assertJsonStructure([
                'data' => [
                    'opportunities' => [
                        [
                            'id',
                            'company_name',
                            'goal',
                            'status' => ['key', 'label', 'color', 'is_current'],
                            'category',
                            'opportunity_number',
                            'investment_required',
                            'sale_percentage',
                            'created_at',
                            'created_at_formatted',
                        ],
                    ],
                    'pagination' => ['current_page', 'last_page', 'per_page', 'total'],
                ],
            ]);
    }

    public function test_company_can_list_own_opportunities_with_purchased_seats_and_no_interests(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $otherCompany = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        $eligibleOpportunity = Opportunity::factory()->create([
            'user_id' => $company->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Eligible Opportunity',
        ]);
        $hasInterestOpportunity = Opportunity::factory()->create([
            'user_id' => $company->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Has Interest Opportunity',
        ]);
        Opportunity::factory()->create([
            'user_id' => $otherCompany->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Other Company Opportunity',
        ]);

        InvestmentSeat::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $eligibleOpportunity->id,
            'price_paid' => 100,
            'purchased_at' => now(),
        ]);
        $seat = InvestmentSeat::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $hasInterestOpportunity->id,
            'price_paid' => 100,
            'purchased_at' => now(),
        ]);

        \App\Models\InterestRequest::query()->create([
            'user_id' => $investor->id,
            'opportunity_id' => $hasInterestOpportunity->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($company);

        $response = $this->getJson('/api/v1/company/opportunities/purchased-seats-without-interests?per_page=10');

        $response->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonCount(1, 'data.opportunities')
            ->assertJsonPath('data.opportunities.0.company_name', 'Eligible Opportunity')
            ->assertJsonPath('data.opportunities.0.statistics.purchased_seats_count', 1)
            ->assertJsonPath('data.opportunities.0.statistics.interest_requests_count', 0);
    }

    public function test_investor_cannot_access_company_purchased_seats_without_interests_endpoint(): void
    {
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        Sanctum::actingAs($investor);

        $response = $this->getJson('/api/v1/company/opportunities/purchased-seats-without-interests');

        $response->assertStatus(403)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.have_no_permission'));
    }

    public function test_sell_business_opportunity_does_not_require_sale_percentage(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $category = Category::factory()->create(['status' => true]);

        Sanctum::actingAs($company);

        $response = $this->postJson('/api/v1/company/opportunities', [
            'goal' => 'sell_business',
            'contact_name' => 'Ahmed Mohamed',
            'contact_phone' => '80808080',
            'contact_email' => 'company@gmail.com',
            'owner_name' => 'Ahmed Mohamed',
            'admin_company_name' => 'Company Admin Name',
            'license_number' => 'LIC-123456',
            'company_name' => 'Existing Business For Sale',
            'category_id' => $category->id,
            'business_age_years' => 3,
            'investment_required' => 65000,
            'business_stage' => 'Operational',
            'legal_entity' => 'Limited Liability Company',
            'financial_status' => 'Stable',
            'investment_reason' => 'Owner wants to exit the business.',
            'full_description' => 'Profitable business offered for full sale.',
            'terms_accepted' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.goal', 'sell_business')
            ->assertJsonPath('data.status.key', OpportunityStatus::Pending->value)
            ->assertJsonPath('data.sale_percentage', null);

        $this->assertDatabaseHas('opportunities', [
            'user_id' => $company->id,
            'goal' => 'sell_business',
            'sale_percentage' => null,
        ]);
    }

    public function test_company_update_resets_opportunity_to_pending(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);

        Sanctum::actingAs($company);

        $opportunity = Opportunity::factory()->create([
            'user_id' => $company->id,
            'status' => OpportunityStatus::NeedsRevision,
            'review_note' => 'Please update the description.',
        ]);

        $response = $this->putJson("/api/v1/company/opportunities/{$opportunity->id}", [
            'goal' => 'sell_business',
            'contact_name' => 'Ahmed Mohamed',
            'contact_phone' => '80808080',
            'contact_email' => 'company@gmail.com',
            'owner_name' => 'Ahmed Mohamed',
            'admin_company_name' => 'Elite Trading Company',
            'license_number' => 'LIC-123456',
            'company_name' => 'Updated Restaurant Opportunity',
            'category_id' => $opportunity->category_id,
            'business_age_years' => 4,
            'investment_required' => 75000,
            'business_stage' => 'Operational',
            'legal_entity' => 'Limited Liability Company',
            'financial_status' => 'Stable',
            'investment_reason' => 'Business restructuring.',
            'full_description' => 'Updated description.',
            'terms_accepted' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status.value', OpportunityStatus::Pending->value)
            ->assertJsonPath('data.review_note', null)
            ->assertJsonPath('data.sale_percentage', null);
    }

    public function test_company_opportunity_details_return_full_status_objects(): void
    {
        GeneralSetting::create([
            'key' => 'completed_deals_commission',
            'value' => '8.5',
        ]);

        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);

        $opportunity = Opportunity::factory()->create([
            'user_id' => $company->id,
            'status' => OpportunityStatus::NeedsRevision,
        ]);

        Sanctum::actingAs($company);

        $response = $this->getJson("/api/v1/company/opportunities/{$opportunity->id}");

        $response->assertOk()
            ->assertJsonPath('data.opportunity_number', $opportunity->fresh()->opportunity_number)
            ->assertJsonPath('data.completed_deals_commission', 8.5)
            ->assertJsonPath('data.status.key', OpportunityStatus::NeedsRevision->value)
            ->assertJsonPath('data.status.is_current', true)
            ->assertJsonCount(5, 'data.statuses');
    }
}
