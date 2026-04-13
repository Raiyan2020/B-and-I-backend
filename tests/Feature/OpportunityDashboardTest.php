<?php

namespace Tests\Feature;

use App\Enums\OpportunityStatus;
use App\Models\Admin;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OpportunityDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        foreach (['opportunities', 'review-opportunity'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
        $role->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    protected function createSuperAdmin(): Admin
    {
        $admin = Admin::factory()->create([
            'password' => 'password123',
        ]);
        $admin->assignRole('super_admin');

        return $admin;
    }

    public function test_admin_can_view_opportunities_index(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.opportunities.index'));

        $response->assertOk()
            ->assertViewIs('dashboard.opportunities.index');
    }

    public function test_admin_can_view_opportunity_details_with_recent_marketplace_activity(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $opportunity = Opportunity::factory()->create();
        $investor = User::factory()->create();

        $seat = InvestmentSeat::create([
            'user_id' => $investor->id,
            'opportunity_id' => $opportunity->id,
            'price_paid' => 2500,
            'purchased_at' => now(),
        ]);

        $interestRequest = InterestRequest::create([
            'user_id' => $investor->id,
            'opportunity_id' => $opportunity->id,
            'investment_seat_id' => $seat->id,
        ]);

        $response = $this->get(route('admin.opportunities.show', $opportunity));

        $response->assertOk()
            ->assertSee(__('dashboard.latest_investment_seats'))
            ->assertSee(__('dashboard.latest_interest_requests'))
            ->assertSee((string) $seat->id)
            ->assertSee((string) $interestRequest->id);
    }

    public function test_admin_can_review_opportunity_with_extended_statuses(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $opportunity = Opportunity::factory()->create([
            'status' => OpportunityStatus::Pending,
        ]);

        $response = $this->postJson(route('admin.opportunities.review', $opportunity), [
            'status' => OpportunityStatus::Reserved->value,
            'review_note' => 'Looks good.',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success');

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'status' => OpportunityStatus::Reserved->value,
            'review_note' => 'Looks good.',
            'reviewed_by_admin_id' => $admin->id,
        ]);
    }
}
