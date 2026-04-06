<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
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

        if (! $superAdminRole->getTranslation('title', 'en')) {
            $superAdminRole->setTranslations('title', [
                'ar' => 'Super Admin',
                'en' => 'Super Admin',
            ]);
            $superAdminRole->save();
        }

        // Create permissions
        $permissions = [
            'roles', 'add-role', 'edit-role', 'show-role', 'delete-role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }

        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    protected function createRoleWithTitles(string $slug, string $ar, string $en): Role
    {
        $role = Role::create([
            'name' => $slug,
            'guard_name' => 'admin',
        ]);
        $role->setTranslations('title', [
            'ar' => $ar,
            'en' => $en,
        ]);
        $role->save();

        return $role;
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

        $this->createRoleWithTitles('test_role', 'اختبار', 'Test Role');

        $response = $this->getJson('/en/admin/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'display_name', 'users_count'],
            ],
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
            'guard_name' => 'admin',
        ]);

        $response = $this->post('/en/admin/roles', [
            'title' => [
                'ar' => 'دور جديد',
                'en' => 'New Role Unique',
            ],
            'permission' => [$permission->id],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $role = Role::where('guard_name', 'admin')
            ->where('title->en', 'New Role Unique')
            ->first();
        $this->assertNotNull($role);
        $this->assertSame('دور جديد', $role->getTranslation('title', 'ar'));
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

        $response->assertSessionHasErrors(['title.ar', 'title.en', 'permission']);
    }

    /**
     * Test role creation prevents duplicate title per locale.
     */
    public function test_role_creation_prevents_duplicate_titles(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $this->createRoleWithTitles('existing_role', 'موجود', 'Duplicate English');

        $permission = Permission::firstOrCreate([
            'name' => 'test-permission',
            'guard_name' => 'admin',
        ]);

        $response = $this->post('/en/admin/roles', [
            'title' => [
                'ar' => 'أخرى',
                'en' => 'Duplicate English',
            ],
            'permission' => [$permission->id],
        ]);

        $response->assertSessionHasErrors('title.en');
    }

    /**
     * Test role creation prevents duplicate Arabic title.
     */
    public function test_role_creation_prevents_duplicate_arabic_titles(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $this->createRoleWithTitles('existing_role_ar', 'نفس العربي', 'Other English');

        $permission = Permission::firstOrCreate([
            'name' => 'test-permission',
            'guard_name' => 'admin',
        ]);

        $response = $this->post('/en/admin/roles', [
            'title' => [
                'ar' => 'نفس العربي',
                'en' => 'Different English',
            ],
            'permission' => [$permission->id],
        ]);

        $response->assertSessionHasErrors('title.ar');
    }

    /**
     * Test authenticated admin can view edit role form.
     */
    public function test_authenticated_admin_can_view_edit_role_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $role = $this->createRoleWithTitles('test_role', 'اختبار', 'Test Role');

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

        $role = $this->createRoleWithTitles('old_role', 'قديم', 'Old Role');

        $permission1 = Permission::firstOrCreate([
            'name' => 'permission1',
            'guard_name' => 'admin',
        ]);

        $permission2 = Permission::firstOrCreate([
            'name' => 'permission2',
            'guard_name' => 'admin',
        ]);

        $response = $this->put("/en/admin/roles/{$role->id}", [
            'title' => [
                'ar' => 'محدث',
                'en' => 'Updated Role',
            ],
            'permission' => [$permission1->id, $permission2->id],
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertSame('old_role', $role->name);
        $this->assertSame('Updated Role', $role->getTranslation('title', 'en'));
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

        $role = $this->createRoleWithTitles('test_role', 'اختبار', 'Test Role');

        $response = $this->put("/en/admin/roles/{$role->id}", []);

        $response->assertSessionHasErrors(['title.ar', 'title.en', 'permission']);
    }

    /**
     * Test authenticated admin can delete role.
     */
    public function test_authenticated_admin_can_delete_role(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $role = $this->createRoleWithTitles('test_role', 'اختبار', 'Test Role');

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
