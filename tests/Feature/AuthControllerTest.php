<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthControllerTest extends TestCase
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
     * Test admin can view login page.
     */
    public function test_admin_can_view_login_page(): void
    {
        $response = $this->get('/en/admin');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.auth.login');
    }

    /**
     * Test admin can login with valid credentials.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->post('/en/admin', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.home'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    /**
     * Test admin cannot login with invalid email.
     */
    public function test_admin_cannot_login_with_invalid_email(): void
    {
        $response = $this->post('/en/admin', [
            'email' => 'wrong@email.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    /**
     * Test admin cannot login with invalid password.
     */
    public function test_admin_cannot_login_with_invalid_password(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->post('/en/admin', [
            'email' => $admin->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('admin');
    }

    /**
     * Test admin cannot login without super_admin role.
     */
    public function test_admin_cannot_login_without_super_admin_role(): void
    {
        $admin = Admin::create([
            'name' => 'Regular Admin',
            'email' => 'regular@test.com',
            'password' => 'password123',
            'phone' => '1234567890',
            'is_blocked' => false,
        ]);

        $response = $this->post('/en/admin', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $this->assertGuest('admin');
    }

    /**
     * Test login validation requires email.
     */
    public function test_login_requires_email(): void
    {
        $response = $this->post('/en/admin', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test login validation requires password.
     */
    public function test_login_requires_password(): void
    {
        $response = $this->post('/en/admin', [
            'email' => 'admin@test.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test admin can logout.
     */
    public function test_admin_can_logout(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/destroy');

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    /**
     * Test authenticated admin can view profile.
     */
    public function test_authenticated_admin_can_view_profile(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/profile');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.auth.profile');
        $response->assertViewHas('admin');
    }

    /**
     * Test unauthenticated admin cannot view profile.
     */
    public function test_unauthenticated_admin_cannot_view_profile(): void
    {
        $response = $this->get('/en/admin/profile');

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test admin can update profile.
     */
    public function test_admin_can_update_profile(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->put('/en/admin/profile/update', [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
            'phone' => '9876543210',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
        ]);
    }

    /**
     * Test admin can update password.
     */
    public function test_admin_can_update_password(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->put('/en/admin/profile/update', [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify new password works
        $this->assertTrue(
            \Hash::check('newpassword123', $admin->fresh()->password)
        );
    }

    /**
     * Test profile update validation requires name.
     */
    public function test_profile_update_requires_name(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->put('/en/admin/profile/update', [
            'email' => $admin->email,
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test profile update validation requires valid email.
     */
    public function test_profile_update_requires_valid_email(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->put('/en/admin/profile/update', [
            'name' => $admin->name,
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test profile update prevents duplicate email.
     */
    public function test_profile_update_prevents_duplicate_email(): void
    {
        $admin1 = $this->createSuperAdmin(['email' => 'admin1@test.com']);
        $admin2 = $this->createSuperAdmin(['email' => 'admin2@test.com']);
        $this->actingAs($admin1, 'admin');

        $response = $this->put('/en/admin/profile/update', [
            'name' => $admin1->name,
            'email' => $admin2->email,
        ]);

        $response->assertSessionHasErrors('email');
    }
}
