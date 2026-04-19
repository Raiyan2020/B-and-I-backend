<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\CompanyInvestorInterestRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyInvestorInterestRequestDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);

        foreach (['users'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $role->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    private function createSuperAdmin(): Admin
    {
        $admin = Admin::factory()->create([
            'password' => 'password123',
        ]);
        $admin->assignRole('super_admin');

        return $admin;
    }

    public function test_admin_can_filter_company_investor_interest_requests(): void
    {
        $admin = $this->createSuperAdmin();
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
            'first_name' => 'Alpha',
            'last_name' => 'Company',
        ]);
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
            'first_name' => 'Ibrahim',
            'last_name' => 'Investor',
        ]);
        $otherCompany = User::factory()->create([
            'role' => UserRole::Advertiser,
            'first_name' => 'Beta',
            'last_name' => 'Company',
        ]);
        $otherInvestor = User::factory()->create([
            'role' => UserRole::Investor,
            'first_name' => 'Omar',
            'last_name' => 'Investor',
        ]);

        $matched = CompanyInvestorInterestRequest::query()->create([
            'company_id' => $company->id,
            'investor_id' => $investor->id,
        ]);
        $matched->update(['created_at' => now()->startOfDay(), 'updated_at' => now()->startOfDay()]);

        CompanyInvestorInterestRequest::query()->create([
            'company_id' => $otherCompany->id,
            'investor_id' => $otherInvestor->id,
        ]);

        $this->actingAs($admin, 'admin');

        $this->getJson(route('admin.company-investor-interest-requests.index', [
            'company_name' => 'Alpha',
            'investor_name' => 'Ibrahim',
            'interest_date' => now()->toDateString(),
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.id', $matched->id)
            ->assertJsonPath('data.0.company_name', $company->name)
            ->assertJsonPath('data.0.investor_name', $investor->name);
    }

    public function test_company_show_page_displays_latest_15_interest_requests_and_hides_wallet_sections(): void
    {
        $admin = $this->createSuperAdmin();
        $company = User::factory()->create([
            'role' => UserRole::Advertiser,
            'first_name' => 'Gamma',
            'last_name' => 'Company',
        ]);

        $firstInvestor = User::factory()->create([
            'role' => UserRole::Investor,
            'first_name' => 'Investor',
            'last_name' => 'One',
        ]);

        $oldestInvestorName = null;
        $latestInvestorName = null;

        for ($i = 0; $i < 16; $i++) {
            $investor = $i === 0
                ? $firstInvestor
                : User::factory()->create([
                    'role' => UserRole::Investor,
                    'first_name' => 'Investor'.$i,
                    'last_name' => 'User',
                ]);

            CompanyInvestorInterestRequest::query()->create([
                'company_id' => $company->id,
                'investor_id' => $investor->id,
            ]);

            if ($i === 0) {
                $oldestInvestorName = $investor->name;
            }

            $latestInvestorName = $investor->name;
        }

        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.users.show', $company));

        $response->assertOk()
            ->assertSee(__('dashboard.latest_company_investor_interest_requests'))
            ->assertSee($latestInvestorName)
            ->assertDontSee($oldestInvestorName)
            ->assertSee('company-investor-interest-requests', false)
            ->assertSee('company_id='.$company->id, false)
            ->assertDontSee(__('dashboard.wallet_balance'))
            ->assertDontSee(__('dashboard.recent_wallet_transactions'))
            ->assertDontSee(__('dashboard.charge_wallet'));
    }
}
