<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerTest extends TestCase
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

        // Create permissions
        $permissions = [
            'users', 'add-user', 'edit-user', 'show-user', 'delete-user', 'block-user'
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
     * Test authenticated admin can view users index.
     */
    public function test_authenticated_admin_can_view_users_index(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.users.index');
    }

    /**
     * Test authenticated admin can view create user form.
     */
    public function test_authenticated_admin_can_view_create_user_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/en/admin/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.users.create');
    }

    /**
     * Test authenticated admin can create user.
     */
    public function test_authenticated_admin_can_create_user(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $image = UploadedFile::fake()->image('user.jpg');

        $response = $this->postJson('/en/admin/users', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'phone' => '501234567',
            'country_code' => '+966',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'image' => $image,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
            'name' => 'New User',
        ]);
    }

    /**
     * Test user creation requires valid data.
     */
    public function test_user_creation_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $response = $this->postJson('/en/admin/users', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'password', 'image']);
    }

    /**
     * Test authenticated admin can view user details.
     */
    public function test_authenticated_admin_can_view_user_details(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->get("/en/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.users.show');
        $response->assertViewHas('row');
    }

    /**
     * Test authenticated admin can view edit user form.
     */
    public function test_authenticated_admin_can_view_edit_user_form(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->get("/en/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.users.edit');
        $response->assertViewHas('row');
    }

    /**
     * Test authenticated admin can update user.
     */
    public function test_authenticated_admin_can_update_user(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->putJson("/en/admin/users/{$user->id}", [
            'name' => 'Updated User',
            'email' => 'updated@test.com',
            'phone' => $user->phone,
            'country_code' => $user->country_code,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'email' => 'updated@test.com',
        ]);
    }

    /**
     * Test authenticated admin can delete user.
     */
    public function test_authenticated_admin_can_delete_user(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->deleteJson("/en/admin/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * Test authenticated admin can delete multiple users.
     */
    public function test_authenticated_admin_can_delete_multiple_users(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->postJson('/en/admin/users/destroy-multiple', [
            'ids' => [$user1->id, $user2->id]
        ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertSoftDeleted('users', ['id' => $user2->id]);
    }

    /**
     * Test authenticated admin can toggle block status.
     */
    public function test_authenticated_admin_can_toggle_block_status(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create(['is_blocked' => false]);

        $response = $this->getJson("/en/admin/users/{$user->id}/toggle-block");

        $response->assertStatus(200);
        $response->assertJson(['key' => 'success']);
        $this->assertTrue($user->fresh()->is_blocked);
    }

    /**
     * Test authenticated admin can toggle active status.
     */
    public function test_authenticated_admin_can_toggle_active_status(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create(['is_active' => false]);

        $response = $this->getJson("/en/admin/users/{$user->id}/toggle-active");

        $response->assertStatus(200);
        $response->assertJson(['key' => 'success']);
        $this->assertTrue($user->fresh()->is_active);
    }

    /**
     * Test authenticated admin can charge user wallet.
     */
    public function test_authenticated_admin_can_charge_user_wallet(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();
        $wallet = Wallet::create([
            'walletable_type' => User::class,
            'walletable_id' => $user->id,
            'available_balance' => 100,
            'reserved_balance' => 0,
        ]);

        $response = $this->postJson("/en/admin/users/{$user->id}/charge-wallet", [
            'amount' => 50,
            'description' => 'Test charge',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['key' => 'success']);
        $this->assertEquals(150, $wallet->fresh()->available_balance);

        // Check transaction was created
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'amount' => 50,
            'type' => WalletTransactionTypeEnum::CHARGE->value,
        ]);
    }

    /**
     * Test wallet charge creates wallet if not exists.
     */
    public function test_wallet_charge_creates_wallet_if_not_exists(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->postJson("/en/admin/users/{$user->id}/charge-wallet", [
            'amount' => 100,
            'description' => 'Initial charge',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('wallets', [
            'walletable_type' => User::class,
            'walletable_id' => $user->id,
            'available_balance' => 100,
        ]);
    }

    /**
     * Test wallet charge validation requires amount.
     */
    public function test_wallet_charge_requires_amount(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->postJson("/en/admin/users/{$user->id}/charge-wallet", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }

    /**
     * Test authenticated admin can send notification to user.
     */
    public function test_authenticated_admin_can_send_notification_to_user(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->postJson("/en/admin/users/{$user->id}/send-notification", [
            'title' => 'Test Notification',
            'body' => 'This is a test notification',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['key' => 'success']);
    }

    /**
     * Test notification sending requires valid data.
     */
    public function test_notification_sending_requires_valid_data(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        $user = User::factory()->create();

        $response = $this->postJson("/en/admin/users/{$user->id}/send-notification", []);

        $response->assertStatus(422);
    }

    /**
     * Test users index returns JSON for AJAX requests.
     */
    public function test_users_index_returns_json_for_ajax_requests(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin, 'admin');

        User::factory()->count(3)->create();

        $response = $this->getJson('/en/admin/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email']
            ]
        ]);
    }
}
