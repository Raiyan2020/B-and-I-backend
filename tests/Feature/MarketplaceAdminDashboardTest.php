<?php

namespace Tests\Feature;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Category;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MarketplaceAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);

        foreach (['investment-seats', 'interest-requests'] as $permission) {
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

    protected function createMarketplaceRecords(): array
    {
        $category = Category::factory()->create();
        $advertiser = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        $opportunity = Opportunity::factory()->create([
            'user_id' => $advertiser->id,
            'category_id' => $category->id,
            'status' => OpportunityStatus::Published,
        ]);

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

        return compact('seat', 'interestRequest');
    }

    public function test_admin_can_view_investment_seats_pages(): void
    {
        $admin = $this->createSuperAdmin();
        $records = $this->createMarketplaceRecords();

        $this->actingAs($admin, 'admin');

        $this->get(route('admin.investment-seats.index'))
            ->assertOk()
            ->assertViewIs('dashboard.investment_seats.index');

        $this->get(route('admin.investment-seats.show', $records['seat']))
            ->assertOk()
            ->assertViewIs('dashboard.investment_seats.show');
    }

    public function test_admin_can_view_interest_requests_pages(): void
    {
        $admin = $this->createSuperAdmin();
        $records = $this->createMarketplaceRecords();

        $this->actingAs($admin, 'admin');

        $this->get(route('admin.interest-requests.index'))
            ->assertOk()
            ->assertViewIs('dashboard.interest_requests.index');

        $this->get(route('admin.interest-requests.show', $records['interestRequest']))
            ->assertOk()
            ->assertViewIs('dashboard.interest_requests.show');
    }

    public function test_admin_can_award_interest_request_and_set_opportunity_investor(): void
    {
        $admin = $this->createSuperAdmin();
        $records = $this->createMarketplaceRecords();

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.interest-requests.award', $records['interestRequest']), [
            'status' => OpportunityStatus::Reserved->value,
        ])->assertOk();

        $records['interestRequest']->opportunity->refresh();

        $this->assertSame(OpportunityStatus::Reserved, $records['interestRequest']->opportunity->status);
        $this->assertSame($records['interestRequest']->user_id, $records['interestRequest']->opportunity->investor_id);
    }

    public function test_opportunity_observer_clears_investor_when_status_returns_to_published(): void
    {
        $records = $this->createMarketplaceRecords();

        $opportunity = $records['interestRequest']->opportunity;

        $opportunity->update([
            'status' => OpportunityStatus::Reserved,
            'investor_id' => $records['interestRequest']->user_id,
        ]);

        $opportunity->update([
            'status' => OpportunityStatus::Published,
        ]);

        $opportunity->refresh();

        $this->assertSame(OpportunityStatus::Published, $opportunity->status);
        $this->assertNull($opportunity->investor_id);
    }

    public function test_admin_can_filter_marketplace_lists_by_opportunity(): void
    {
        $admin = $this->createSuperAdmin();
        $first = $this->createMarketplaceRecords();
        $second = $this->createMarketplaceRecords();

        $this->actingAs($admin, 'admin');

        $this->getJson(route('admin.investment-seats.index', [
            'opportunity_id' => $first['interestRequest']->opportunity_id,
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.id', $first['seat']->id)
            ->assertJsonPath('data.0.opportunity_id', $first['interestRequest']->opportunity_id);

        $this->getJson(route('admin.interest-requests.index', [
            'opportunity_id' => $first['interestRequest']->opportunity_id,
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.id', $first['interestRequest']->id)
            ->assertJsonPath('data.0.opportunity_id', $first['interestRequest']->opportunity_id);
    }
}
