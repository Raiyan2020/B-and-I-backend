<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FcmToken;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationsControllerTest extends TestCase
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
            'show-settings', 'edit-settings'
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
     * Test authenticated admin can mark all notifications as read.
     */
    public function test_authenticated_admin_can_mark_all_notifications_as_read(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Create unread notifications
        Notification::factory()->count(3)->create([
            'admin_id' => $admin->id,
            'seen' => 0,
        ]);

        $response = $this->get('/en/admin/notifications/read-all');

        $response->assertRedirect();
        $this->assertEquals(0, Notification::where('admin_id', $admin->id)->where('seen', 0)->count());
    }

    /**
     * Test authenticated admin can mark single notification as read.
     */
    public function test_authenticated_admin_can_mark_single_notification_as_read(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $notification = Notification::factory()->create([
            'admin_id' => $admin->id,
            'seen' => 0,
        ]);

        $response = $this->get("/en/admin/notifications/{$notification->id}/read");

        $response->assertRedirect();
        $this->assertEquals(1, $notification->fresh()->seen);
    }

    /**
     * Test authenticated admin can update FCM token.
     */
    public function test_authenticated_admin_can_update_fcm_token(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $token = 'test-fcm-token-12345';

        $response = $this->patchJson('/en/admin/fcm-token', [
            'token' => $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('fcm_tokens', [
            'admin_id' => $admin->id,
            'tokens' => $token,
        ]);
    }

    /**
     * Test FCM token update creates new record if not exists.
     */
    public function test_fcm_token_update_creates_new_record_if_not_exists(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $token = 'new-fcm-token-12345';

        $this->assertDatabaseMissing('fcm_tokens', [
            'tokens' => $token,
        ]);

        $response = $this->patchJson('/en/admin/fcm-token', [
            'token' => $token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('fcm_tokens', [
            'admin_id' => $admin->id,
            'tokens' => $token,
        ]);
    }

    /**
     * Test FCM token update updates existing record.
     */
    public function test_fcm_token_update_updates_existing_record(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $oldToken = 'old-fcm-token-12345';
        $newToken = 'new-fcm-token-12345';

        FcmToken::create([
            'admin_id' => $admin->id,
            'tokens' => $oldToken,
        ]);

        $response = $this->patchJson('/en/admin/fcm-token', [
            'token' => $newToken,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('fcm_tokens', [
            'admin_id' => $admin->id,
            'tokens' => $newToken,
        ]);

        $this->assertDatabaseMissing('fcm_tokens', [
            'tokens' => $oldToken,
        ]);
    }

    /**
     * Test FCM token update handles errors gracefully.
     */
    public function test_fcm_token_update_handles_errors_gracefully(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        // Force an error by using invalid data
        // The controller should catch exceptions and return error response
        $response = $this->patchJson('/en/admin/fcm-token', [
            'token' => null,
        ]);

        // Should handle gracefully (either validation error or 500)
        $this->assertContains($response->status(), [422, 500]);
    }

    /**
     * Test unauthenticated admin cannot mark notifications as read.
     */
    public function test_unauthenticated_admin_cannot_mark_notifications_as_read(): void
    {
        $response = $this->get('/en/admin/notifications/read-all');

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test unauthenticated admin cannot update FCM token.
     */
    public function test_unauthenticated_admin_cannot_update_fcm_token(): void
    {
        $response = $this->patchJson('/en/admin/fcm-token', [
            'token' => 'test-token',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test mark all notifications only affects current admin.
     */
    public function test_mark_all_notifications_only_affects_current_admin(): void
    {
        $admin1 = $this->createSuperAdmin(['email' => 'admin1@test.com']);
        $admin2 = $this->createSuperAdmin(['email' => 'admin2@test.com']);

        // Create notifications for both admins
        Notification::factory()->create([
            'admin_id' => $admin1->id,
            'seen' => 0,
        ]);

        Notification::factory()->create([
            'admin_id' => $admin2->id,
            'seen' => 0,
        ]);

        $this->actingAs($admin1, 'admin');
        $this->get('/en/admin/notifications/read-all');

        // Admin1's notification should be read
        $this->assertEquals(1, Notification::where('admin_id', $admin1->id)->where('seen', 1)->count());

        // Admin2's notification should still be unread
        $this->assertEquals(1, Notification::where('admin_id', $admin2->id)->where('seen', 0)->count());
    }
}
