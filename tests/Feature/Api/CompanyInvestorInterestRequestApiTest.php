<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\CompanyInvestorInterestRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyInvestorInterestRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_create_interest_request_for_investor(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        Sanctum::actingAs($company);

        $response = $this->postJson('/api/v1/company/investor-interest-requests', [
            'investor_id' => $investor->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.company_investor_interest_request_created'))
            ->assertJsonPath('data.company.id', $company->id)
            ->assertJsonPath('data.investor.id', $investor->id);

        $this->assertDatabaseHas('company_investor_interest_requests', [
            'company_id' => $company->id,
            'investor_id' => $investor->id,
        ]);
    }

    public function test_company_cannot_submit_duplicate_interest_request_for_same_investor(): void
    {
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        CompanyInvestorInterestRequest::query()->create([
            'company_id' => $company->id,
            'investor_id' => $investor->id,
        ]);

        Sanctum::actingAs($company);

        $response = $this->postJson('/api/v1/company/investor-interest-requests', [
            'investor_id' => $investor->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath(
                'response_status.validation_errors.investor_id.0',
                __('apis.company_investor_interest_already_submitted')
            );

        $this->assertSame(1, CompanyInvestorInterestRequest::query()->count());
    }

    public function test_investor_cannot_create_company_interest_request(): void
    {
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);
        $otherInvestor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        Sanctum::actingAs($investor);

        $response = $this->postJson('/api/v1/company/investor-interest-requests', [
            'investor_id' => $otherInvestor->id,
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.have_no_permission'));

        $this->assertSame(0, CompanyInvestorInterestRequest::query()->count());
    }
}
