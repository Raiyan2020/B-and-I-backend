<?php

namespace Tests\Feature\Api;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OpportunityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_create_opportunity_in_pending_review_status(): void
    {
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
            ->assertJsonPath('data.status', OpportunityStatus::PendingReview->value)
            ->assertJsonPath('data.sale_percentage', 20);

        $this->assertDatabaseHas('opportunities', [
            'user_id' => $company->id,
            'status' => OpportunityStatus::PendingReview->value,
            'company_name' => 'Restaurant Opportunity No. 10',
        ]);
    }

    public function test_only_approved_opportunities_are_visible_publicly(): void
    {
        $category = Category::factory()->create(['status' => true]);

        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::PendingReview,
            'company_name' => 'Pending Company',
        ]);

        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Approved,
            'company_name' => 'Approved Company',
        ]);

        $response = $this->getJson('/api/v1/general/opportunities');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.company_name', 'Approved Company');
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
            ->assertJsonPath('data.sale_percentage', null);

        $this->assertDatabaseHas('opportunities', [
            'user_id' => $company->id,
            'goal' => 'sell_business',
            'sale_percentage' => null,
        ]);
    }

    public function test_company_update_resets_opportunity_to_pending_review(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);

        Sanctum::actingAs($company);

        $opportunity = Opportunity::factory()->create([
            'user_id' => $company->id,
            'status' => OpportunityStatus::NeedsModification,
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
            ->assertJsonPath('data.status', OpportunityStatus::PendingReview->value)
            ->assertJsonPath('data.review_note', null)
            ->assertJsonPath('data.sale_percentage', null);
    }
}
