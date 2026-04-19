<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AccountDeletionRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Device;
use App\Models\FcmToken;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\Notifications\FirebaseNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountDeletionRequestDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);

        foreach (['users', 'edit-user'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $role->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    private function createSuperAdmin(): Admin
    {
        $admin = Admin::factory()->create([
            'password' => 'password123',
        ]);
        $admin->assignRole('super_admin');

        return $admin;
    }

    public function test_admin_can_view_account_deletion_request_details_with_active_relations(): void
    {
        $admin = $this->createSuperAdmin();
        $category = Category::factory()->create();

        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
            'category_id' => $category->id,
            'email' => 'advertiser@example.test',
        ]);
        $otherAdvertiser = User::factory()->create([
            'role' => UserRole::Advertiser,
            'category_id' => $category->id,
        ]);

        $ownAdvertisement = Opportunity::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'company_name' => 'Own Active Ad',
        ]);

        $targetOpportunity = Opportunity::factory()->create([
            'user_id' => $otherAdvertiser->id,
            'category_id' => $category->id,
            'status' => 'reserved',
            'company_name' => 'Target Active Ad',
        ]);

        $seat = InvestmentSeat::query()->create([
            'user_id' => $user->id,
            'opportunity_id' => $targetOpportunity->id,
            'price_paid' => 2500,
            'purchased_at' => now(),
        ]);

        $interestRequest = InterestRequest::query()->create([
            'user_id' => $user->id,
            'opportunity_id' => $targetOpportunity->id,
            'investment_seat_id' => $seat->id,
        ]);

        $accountDeletionRequest = AccountDeletionRequest::query()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin');

        $this->get(route('admin.account-deletion-requests.show', $accountDeletionRequest))
            ->assertOk()
            ->assertViewIs('dashboard.account_deletion_requests.show')
            ->assertSee(__('dashboard.account_deletion_request_details'))
            ->assertSee(__('dashboard.active_advertisements'))
            ->assertSee(__('dashboard.active_purchased_seats'))
            ->assertSee(__('dashboard.active_interest_requests'))
            ->assertSee((string) $ownAdvertisement->id)
            ->assertSee((string) $seat->id)
            ->assertSee((string) $interestRequest->id);
    }

    public function test_admin_can_approve_account_deletion_request_and_cleanup_user_access(): void
    {
        $firebase = Mockery::mock(FirebaseNotificationService::class);
        $firebase->shouldReceive('sendToUser')
            ->once()
            ->andReturn(['sent' => 1, 'failed' => 0, 'responses' => []]);
        $this->app->instance(FirebaseNotificationService::class, $firebase);

        $admin = $this->createSuperAdmin();
        $user = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        Device::query()->create([
            'user_id' => $user->id,
            'token' => 'delete-account-device',
            'device_type' => 'android',
            'locale' => 'en',
        ]);

        FcmToken::query()->create([
            'user_id' => $user->id,
            'tokens' => 'push-token',
        ]);

        $user->authUpdates()->create([
            'type' => 'email',
            'sub_type' => 'new_email',
            'attribute' => 'new@example.test',
            'code' => '123456',
            'code_expires_at' => now()->addMinutes(10),
        ]);

        $user->createToken('delete-account-token');

        $accountDeletionRequest = AccountDeletionRequest::query()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin');

        $this->postJson(route('admin.account-deletion-requests.review', $accountDeletionRequest), [
            'status' => 'approved',
        ])->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('dashboard.account_deletion_request_review_saved'));

        $accountDeletionRequest->refresh();

        $this->assertSame('approved', $accountDeletionRequest->status->value);
        $this->assertSame($admin->id, $accountDeletionRequest->reviewed_by_admin_id);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSame(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->count());
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'delete-account-device',
        ]);
        $this->assertDatabaseMissing('fcm_tokens', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('auth_updates', [
            'auth_updateable_type' => User::class,
            'auth_updateable_id' => $user->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'notification_type' => 'account_deletion_request.approved',
        ]);
    }

    public function test_admin_can_reject_account_deletion_request(): void
    {
        $firebase = Mockery::mock(FirebaseNotificationService::class);
        $firebase->shouldReceive('sendToUser')
            ->once()
            ->andReturn(['sent' => 1, 'failed' => 0, 'responses' => []]);
        $this->app->instance(FirebaseNotificationService::class, $firebase);

        $admin = $this->createSuperAdmin();
        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
        ]);

        $accountDeletionRequest = AccountDeletionRequest::query()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin, 'admin');

        $this->postJson(route('admin.account-deletion-requests.review', $accountDeletionRequest), [
            'status' => 'rejected',
            'rejection_reason' => 'Active contract still requires the account.',
        ])->assertOk();

        $accountDeletionRequest->refresh();

        $this->assertSame('rejected', $accountDeletionRequest->status->value);
        $this->assertSame('Active contract still requires the account.', $accountDeletionRequest->rejection_reason);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'notification_type' => 'account_deletion_request.rejected',
        ]);
    }
}
