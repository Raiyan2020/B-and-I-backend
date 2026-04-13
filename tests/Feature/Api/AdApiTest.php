<?php

namespace Tests\Feature\Api;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_ads_listing_hides_private_sections_and_includes_seat_price(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '49.50']);
        $category = Category::factory()->create(['status' => true]);

        Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
        ]);

        $response = $this->getJson('/api/v1/general/opportunities');

        $response->assertOk()
            ->assertJsonPath('data.opportunities.0.seat_price', 49.5)
            ->assertJsonPath('data.opportunities.0.has_seat', null)
            ->assertJsonMissingPath('data.opportunities.0.contact_name')
            ->assertJsonMissingPath('data.opportunities.0.legal_entity')
            ->assertJsonMissingPath('data.opportunities.0.business_age_years')
            ->assertJsonMissingPath('data.opportunities.0.created_at');
    }

    public function test_authenticated_investor_gets_correct_per_ad_flags(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '25']);
        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);

        $buyableAd = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Buyable Ad',
        ]);

        $reservedAd = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Reserved,
            'company_name' => 'Reserved Ad',
        ]);

        $interestAd = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Interest Ad',
        ]);

        $seat = InvestmentSeat::create([
            'user_id' => $investor->id,
            'opportunity_id' => $interestAd->id,
            'price_paid' => 25,
            'purchased_at' => now(),
        ]);

        InterestRequest::create([
            'user_id' => $investor->id,
            'opportunity_id' => $interestAd->id,
            'investment_seat_id' => $seat->id,
        ]);

        Sanctum::actingAs($investor);

        $response = $this->getJson('/api/v1/general/opportunities');

        $response->assertOk()
            ->assertJsonFragment([
                'company_name' => 'Buyable Ad',
                'has_seat' => false,
                'can_buy_seat' => true,
                'can_submit_interest' => false,
                'has_submitted_interest' => false,
            ])
            ->assertJsonFragment([
                'company_name' => 'Reserved Ad',
                'has_seat' => false,
                'can_buy_seat' => false,
                'can_submit_interest' => false,
                'has_submitted_interest' => false,
            ])
            ->assertJsonFragment([
                'company_name' => 'Interest Ad',
                'has_seat' => true,
                'can_buy_seat' => false,
                'can_submit_interest' => false,
                'has_submitted_interest' => true,
            ]);
    }

    public function test_ad_owner_sees_own_listing_and_unlocked_details_without_purchase(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '49.50']);

        $category = Category::factory()->create(['status' => true]);
        $advertiser = User::factory()->create(['role' => UserRole::Advertiser]);
        $ad = Opportunity::factory()->create([
            'user_id' => $advertiser->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'company_name' => 'Owner Ad',
            'legal_entity' => 'Owner LLC',
            'financial_status' => 'Strong',
            'investment_reason' => 'Owner reason',
            'full_description' => 'Owner full description',
        ]);

        Sanctum::actingAs($advertiser);

        $listResponse = $this->getJson('/api/v1/general/opportunities');

        $listResponse->assertOk()
            ->assertJsonFragment([
                'company_name' => 'Owner Ad',
                'is_owner' => true,
                'can_buy_seat' => false,
                'can_submit_interest' => false,
            ]);

        $detailResponse = $this->getJson("/api/v1/general/opportunities/{$ad->id}");

        $detailResponse->assertOk()
            ->assertJsonPath('data.is_owner', true)
            ->assertJsonPath('data.file_access.key', 'open')
            ->assertJsonPath('data.file_access.is_open', true)
            ->assertJsonPath('data.can_buy_seat', false)
            ->assertJsonPath('data.can_submit_interest', false)
            ->assertJsonPath('data.legal_entity', 'Owner LLC')
            ->assertJsonPath('data.full_description', 'Owner full description');
    }

    public function test_investor_can_purchase_seat_and_get_unlocked_opportunity_details(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '49.50']);
        GeneralSetting::create(['key' => 'completed_deals_commission', 'value' => '7.50']);
        GeneralSetting::create(['key' => 'contact_phone', 'value' => '+96522223333']);
        GeneralSetting::create(['key' => 'contact_email', 'value' => 'deals@example.test']);

        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);
        $ad = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'legal_entity' => 'LLC',
            'financial_status' => 'Healthy',
            'investment_reason' => 'Expansion',
            'full_description' => 'Detailed description',
        ]);

        Sanctum::actingAs($investor);

        $purchaseResponse = $this->postJson("/api/v1/general/opportunities/{$ad->id}/seats");

        $purchaseResponse->assertStatus(201)
            ->assertJsonPath('data.seat.opportunity_id', $ad->id)
            ->assertJsonPath('data.seat.price_paid', 49.5)
            ->assertJsonPath('data.opportunity.has_seat', true)
            ->assertJsonPath('data.opportunity.file_access.key', 'open')
            ->assertJsonPath('data.opportunity.admin_contact_phone', '+96522223333')
            ->assertJsonPath('data.opportunity.admin_contact_email', 'deals@example.test')
            ->assertJsonPath('data.opportunity.legal_entity', 'LLC');

        $this->assertDatabaseHas('investment_seats', [
            'user_id' => $investor->id,
            'opportunity_id' => $ad->id,
        ]);
    }

    public function test_investor_can_submit_interest_only_after_buying_seat(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '49.50']);

        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);
        $ad = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
        ]);

        Sanctum::actingAs($investor);

        $blockedResponse = $this->postJson("/api/v1/general/opportunities/{$ad->id}/interest-requests");

        $blockedResponse->assertStatus(422)
            ->assertJsonPath('response_status.validation_errors.seat.0', __('apis.interest_requires_seat_purchase'));

        InvestmentSeat::create([
            'user_id' => $investor->id,
            'opportunity_id' => $ad->id,
            'price_paid' => 49.5,
            'purchased_at' => now(),
        ]);

        $interestResponse = $this->postJson("/api/v1/general/opportunities/{$ad->id}/interest-requests");

        $interestResponse->assertStatus(201)
            ->assertJsonPath('data.interest_request.opportunity_id', $ad->id)
            ->assertJsonPath('data.opportunity.has_seat', true)
            ->assertJsonPath('data.opportunity.has_submitted_interest', true)
            ->assertJsonPath('data.opportunity.can_submit_interest', false);

        $this->assertDatabaseHas('interest_requests', [
            'user_id' => $investor->id,
            'opportunity_id' => $ad->id,
        ]);
    }

    public function test_ad_details_include_commission_and_localized_file_access_state(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '49.50']);
        GeneralSetting::create(['key' => 'completed_deals_commission', 'value' => '7.50']);

        $category = Category::factory()->create(['status' => true]);
        $investor = User::factory()->create(['role' => UserRole::Investor]);

        $ad = Opportunity::factory()->create([
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
            'legal_entity' => 'LLC',
            'financial_status' => 'Healthy',
            'investment_reason' => 'Expansion',
            'full_description' => 'Detailed description',
        ]);

        $guestResponse = $this->withHeaders([
            'Accept-Language' => 'en',
        ])->getJson("/api/v1/general/opportunities/{$ad->id}");

        $guestResponse->assertOk()
            ->assertJsonPath('data.completed_deals_commission', 7.5)
            ->assertJsonPath('data.current_locale', 'en')
            ->assertJsonPath('data.file_access.key', 'locked')
            ->assertJsonPath('data.file_access.label', trans('apis.file_locked', [], 'en'))
            ->assertJsonPath('data.file_access.is_open', false)
            ->assertJsonMissingPath('data.legal_entity')
            ->assertJsonMissingPath('data.admin_contact_phone')
            ->assertJsonMissingPath('data.admin_contact_email');

        InvestmentSeat::create([
            'user_id' => $investor->id,
            'opportunity_id' => $ad->id,
            'price_paid' => 49.50,
            'purchased_at' => now(),
        ]);

        Sanctum::actingAs($investor);

        $investorResponse = $this->withHeaders([
            'Accept-Language' => 'ar',
        ])->getJson("/api/v1/general/opportunities/{$ad->id}");

        $investorResponse->assertOk()
            ->assertJsonPath('data.current_locale', 'ar')
            ->assertJsonPath('data.file_access.key', 'open')
            ->assertJsonPath('data.file_access.label', trans('apis.file_open', [], 'ar'))
            ->assertJsonPath('data.file_access.is_open', true)
            ->assertJsonPath('data.legal_entity', 'LLC');
    }

    public function test_ad_owner_can_update_only_when_needs_revision(): void
    {
        GeneralSetting::create(['key' => 'seat_price', 'value' => '50']);
        $advertiser = User::factory()->create(['role' => UserRole::Advertiser]);
        $category = Category::factory()->create(['status' => true]);
        $ad = Opportunity::factory()->create([
            'user_id' => $advertiser->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::NeedsRevision,
            'review_note' => 'Please revise.',
        ]);

        Sanctum::actingAs($advertiser);

        $response = $this->putJson("/api/v1/company/opportunities/{$ad->id}", [
            'goal' => 'sell_business',
            'company_name' => 'Updated Ad',
            'category_id' => $category->id,
            'business_age_years' => 5,
            'investment_required' => 99000,
            'business_stage' => 'Operational',
            'legal_entity' => 'LLC',
            'financial_status' => 'Healthy',
            'investment_reason' => 'Expansion',
            'full_description' => 'Updated description',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status.value', OpportunityStatus::Pending->value)
            ->assertJsonPath('data.legal_entity', 'LLC')
            ->assertJsonMissingPath('data.contact_name');

        $this->assertDatabaseHas('opportunities', [
            'id' => $ad->id,
            'company_name' => 'Updated Ad',
            'status' => OpportunityStatus::Pending->value,
            'review_note' => null,
        ]);
    }

    public function test_ad_update_returns_validation_error_for_non_revision_status(): void
    {
        $advertiser = User::factory()->create(['role' => UserRole::Advertiser]);
        $category = Category::factory()->create(['status' => true]);
        $ad = Opportunity::factory()->create([
            'user_id' => $advertiser->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
        ]);

        Sanctum::actingAs($advertiser);

        $response = $this->putJson("/api/v1/company/opportunities/{$ad->id}", [
            'goal' => 'sell_business',
            'company_name' => 'Updated Ad',
            'category_id' => $category->id,
            'business_age_years' => 5,
            'investment_required' => 99000,
            'business_stage' => 'Operational',
            'legal_entity' => 'LLC',
            'financial_status' => 'Healthy',
            'investment_reason' => 'Expansion',
            'full_description' => 'Updated description',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('response_status.validation_errors.status.0', __('apis.ad_edit_requires_needs_revision'));
    }
}
