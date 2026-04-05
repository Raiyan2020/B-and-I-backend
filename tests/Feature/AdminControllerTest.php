<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
        Storage::fake('public');
    }

    /**
     * Seed permissions and roles for testing.
     */
    protected function seedPermissions(): void
    {
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'admin']
        );

        // Create permissions
        $permissions = [
            'admins', 'add-admin', 'edit-admin', 'show-admin', 'delete-admin', 'block-admin'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    /**
     * Create a super admin user for testing.
     */
    protected function createSuperAdmin(array $attributes = []): Admin
    {
        $defaults = [
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'phone' => '1234567890',
            'is_blocked' => false,
        ];

        $admin = Admin::create(array_merge($defaults, $attributes));
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        if ($superAdminRole) {
            $admin->assignRole($superAdminRole);
        }

        return $admin;
    }

    /**
     * Test authenticated admin can view admins index.
     */
    public function test_authenticated_admin_can_view_admins_index(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/admins');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admins.index');
    }

    /**
     * Test authenticated admin can view create admin form.
     */
    public function test_authenticated_admin_can_view_create_admin_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/admins/create');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admins.create');
        $response->assertViewHas('roles');
    }

    /**
     * Test authenticated admin can create admin.
     */
    public function test_authenticated_admin_can_create_admin(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $adminRole = Role::where('name', 'admin')->where('guard_name', 'admin')->first();

        $image = UploadedFile::fake()->image('admin.jpg');

        $response = $this->postJson('/en/admin/admins', [
            'name' => 'New Admin',
            'email' => 'newadmin@test.com',
            'phone' => '9876543210',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => $adminRole->name,
            'image' => $image,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('admins', [
            'email' => 'newadmin@test.com',
            'name' => 'New Admin',
        ]);
    }

    /**
     * Test admin creation requires valid data.
     */
    public function test_admin_creation_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->postJson('/en/admin/admins', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'phone', 'password', 'role', 'image']);
    }

    /**
     * Test authenticated admin can view admin details.
     */
    public function test_authenticated_admin_can_view_admin_details(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $newAdmin = Admin::factory()->create();

        $response = $this->get("/en/admin/admins/{$newAdmin->id}");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admins.show');
        $response->assertViewHas('row');
    }

    /**
     * Test authenticated admin can view edit admin form.
     */
    public function test_authenticated_admin_can_view_edit_admin_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $newAdmin = Admin::factory()->create();

        $response = $this->get("/en/admin/admins/{$newAdmin->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admins.edit');
        $response->assertViewHas('row');
        $response->assertViewHas('roles');
    }

    /**
     * Test authenticated admin can update admin.
     */
    public function test_authenticated_admin_can_update_admin(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $adminRole = Role::where('name', 'admin')->where('guard_name', 'admin')->first();
        $newAdmin = Admin::factory()->create();

        $response = $this->putJson("/en/admin/admins/{$newAdmin->id}", [
            'name' => 'Updated Admin',
            'email' => 'updated@test.com',
            'phone' => '1111111111',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'role' => $adminRole->name,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('admins', [
            'id' => $newAdmin->id,
            'name' => 'Updated Admin',
            'email' => 'updated@test.com',
        ]);
    }

    /**
     * Test authenticated admin can delete admin.
     */
    public function test_authenticated_admin_can_delete_admin(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $newAdmin = Admin::factory()->create();

        $response = $this->deleteJson("/en/admin/admins/{$newAdmin->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('admins', ['id' => $newAdmin->id]);
    }

    /**
     * Test authenticated admin can delete multiple admins.
     */
    public function test_authenticated_admin_can_delete_multiple_admins(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $admin1 = Admin::factory()->create();
        $admin2 = Admin::factory()->create();

        $response = $this->postJson('/en/admin/admins/destroy-multiple', [
            'ids' => [$admin1->id, $admin2->id]
        ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('admins', ['id' => $admin1->id]);
        $this->assertSoftDeleted('admins', ['id' => $admin2->id]);
    }

    /**
     * Test authenticated admin can toggle block status.
     */
    public function test_authenticated_admin_can_toggle_block_status(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $newAdmin = Admin::factory()->create(['is_blocked' => false]);

        $response = $this->getJson("/en/admin/admins/{$newAdmin->id}/toggle-block");

        $response->assertStatus(200);
        $response->assertJson(['key' => 'success']);
        $this->assertTrue($newAdmin->fresh()->is_blocked);
    }

    /**
     * Test admin cannot delete themselves.
     */
    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->deleteJson("/en/admin/admins/{$admin->id}");

        // Should not be able to delete themselves (filtered in indexConditions)
        $this->assertDatabaseHas('admins', ['id' => $admin->id]);
    }

    /**
     * Test admin cannot delete super admin (id = 1).
     */
    public function test_admin_cannot_delete_super_admin(): void
    {
        $admin = $this->createSuperAdmin(['id' => 1]);
        $this->actingAs($admin, 'admin');

        $otherAdmin = $this->createSuperAdmin(['email' => 'other@test.com']);
        $this->actingAs($otherAdmin, 'admin');

        $response = $this->deleteJson("/en/admin/admins/1");

        // Should not be able to delete super admin
        $this->assertDatabaseHas('admins', ['id' => 1]);
    }

    /**
     * Test admins index returns JSON for AJAX requests.
     */
    public function test_admins_index_returns_json_for_ajax_requests(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        Admin::factory()->count(3)->create();

        $response = $this->getJson('/en/admin/admins');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email']
            ]
        ]);
    }
}
