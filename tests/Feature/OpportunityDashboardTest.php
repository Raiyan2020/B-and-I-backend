<?php

namespace Tests\Feature;

use App\Enums\OpportunityStatus;
use App\Models\Admin;
use App\Models\Opportunity;
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

    public function test_admin_can_review_opportunity(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $opportunity = Opportunity::factory()->create([
            'status' => OpportunityStatus::PendingReview,
        ]);

        $response = $this->postJson(route('admin.opportunities.review', $opportunity), [
            'status' => OpportunityStatus::Approved->value,
            'review_note' => 'Looks good.',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success');

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'status' => OpportunityStatus::Approved->value,
            'review_note' => 'Looks good.',
            'reviewed_by_admin_id' => $admin->id,
        ]);
    }
}
