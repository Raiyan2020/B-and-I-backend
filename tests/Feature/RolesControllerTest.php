<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissions();
    }

    /**
     * Seed permissions and roles for testing.
     */
    protected function seedPermissions(): void
    {
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );

        // Create permissions
        $permissions = [
            'roles', 'add-role', 'edit-role', 'show-role', 'delete-role'
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
     * Test authenticated admin can view roles index.
     */
    public function test_authenticated_admin_can_view_roles_index(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/roles');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.roles.list');
    }

    /**
     * Test roles index returns JSON for AJAX requests.
     */
    public function test_roles_index_returns_json_for_ajax_requests(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        Role::create([
            'name' => 'test_role',
            'guard_name' => 'admin',
        ]);

        $response = $this->getJson('/en/admin/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'users_count']
            ]
        ]);
    }

    /**
     * Test authenticated admin can view create role form.
     */
    public function test_authenticated_admin_can_view_create_role_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/roles/create');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.roles.add');
        $response->assertViewHas('permissions');
    }

    /**
     * Test authenticated admin can create role.
     */
    public function test_authenticated_admin_can_create_role(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $permission = Permission::firstOrCreate([
            'name' => 'test-permission',
            'guard_name' => 'admin'
        ]);

        $response = $this->post('/en/admin/roles', [
            'name' => 'new_role',
            'permission' => [$permission->id],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', [
            'name' => 'new_role',
            'guard_name' => 'admin',
        ]);

        $role = Role::where('name', 'new_role')->first();
        $this->assertTrue($role->hasPermissionTo($permission));
    }

    /**
     * Test role creation requires valid data.
     */
    public function test_role_creation_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->post('/en/admin/roles', []);

        $response->assertSessionHasErrors(['name', 'permission']);
    }

    /**
     * Test role creation prevents duplicate names.
     */
    public function test_role_creation_prevents_duplicate_names(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        Role::create([
            'name' => 'existing_role',
            'guard_name' => 'admin',
        ]);

        $permission = Permission::firstOrCreate([
            'name' => 'test-permission',
            'guard_name' => 'admin'
        ]);

        $response = $this->post('/en/admin/roles', [
            'name' => 'existing_role',
            'permission' => [$permission->id],
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test authenticated admin can view edit role form.
     */
    public function test_authenticated_admin_can_view_edit_role_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $role = Role::create([
            'name' => 'test_role',
            'guard_name' => 'admin',
        ]);

        $response = $this->get("/en/admin/roles/{$role->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.roles.edit');
        $response->assertViewHas('role');
        $response->assertViewHas('permissions');
    }

    /**
     * Test authenticated admin can update role.
     */
    public function test_authenticated_admin_can_update_role(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $role = Role::create([
            'name' => 'old_role',
            'guard_name' => 'admin',
        ]);

        $permission1 = Permission::firstOrCreate([
            'name' => 'permission1',
            'guard_name' => 'admin'
        ]);

        $permission2 = Permission::firstOrCreate([
            'name' => 'permission2',
            'guard_name' => 'admin'
        ]);

        $response = $this->put("/en/admin/roles/{$role->id}", [
            'name' => 'updated_role',
            'permission' => [$permission1->id, $permission2->id],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated_role',
        ]);

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo($permission1));
        $this->assertTrue($role->hasPermissionTo($permission2));
    }

    /**
     * Test role update validation requires valid data.
     */
    public function test_role_update_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $role = Role::create([
            'name' => 'test_role',
            'guard_name' => 'admin',
        ]);

        $response = $this->put("/en/admin/roles/{$role->id}", []);

        $response->assertSessionHasErrors(['name', 'permission']);
    }

    /**
     * Test authenticated admin can delete role.
     */
    public function test_authenticated_admin_can_delete_role(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $role = Role::create([
            'name' => 'test_role',
            'guard_name' => 'admin',
        ]);

        $response = $this->delete("/en/admin/roles/{$role->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    /**
     * Test admin cannot delete super_admin role.
     */
    public function test_admin_cannot_delete_super_admin_role(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();

        $response = $this->delete("/en/admin/roles/{$superAdminRole->id}");

        // Should still exist (filtered in index)
        $this->assertDatabaseHas('roles', [
            'id' => $superAdminRole->id,
            'name' => 'super_admin',
        ]);
    }

    /**
     * Test roles index excludes super_admin role.
     */
    public function test_roles_index_excludes_super_admin_role(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->getJson('/en/admin/roles');

        $response->assertStatus(200);
        $data = $response->json('data');
        $superAdminFound = collect($data)->contains(function ($role) {
            return $role['name'] === 'super_admin';
        });
        $this->assertFalse($superAdminFound);
    }
}
