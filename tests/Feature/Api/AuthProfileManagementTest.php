<?php

namespace Tests\Feature\Api;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Device;
use App\Models\PreferredSector;
use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_investor_can_update_profile_details(): void
    {
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password123',
        ]);
        $preferredSector = PreferredSector::query()->create([
            'name' => ['ar' => 'التقنية', 'en' => 'Technology'],
            'status' => true,
        ]);
        $category = Category::factory()->create(['status' => true]);

        Sanctum::actingAs($investor);

        $response = $this->patchJson('/api/v1/auth/profile', [
            'display_name' => 'Verified Investor',
            'bio' => 'نبذة للمستثمر',
            'short_description' => 'وصف مختصر',
            'available_capital' => 250000,
            'preferred_sector_id' => $preferredSector->id,
            'category_id' => $category->id,
            'investor_experience' => InvestorExperience::Beginner->value,
            'investor_type' => InvestorType::Angel->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.display_name', 'Verified Investor')
            ->assertJsonPath('data.bio', 'نبذة للمستثمر')
            ->assertJsonPath('data.short_description', 'وصف مختصر')
            ->assertJsonPath('data.available_capital', '250000.000')
            ->assertJsonPath('data.preferred_sector.id', $preferredSector->id)
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.investor_experience', InvestorExperience::Beginner->value)
            ->assertJsonPath('data.investor_type', InvestorType::Angel->value);

        $this->assertDatabaseHas('users', [
            'id' => $investor->id,
            'display_name' => 'Verified Investor',
            'bio' => 'نبذة للمستثمر',
            'short_description' => 'وصف مختصر',
            'available_capital' => 250000,
            'preferred_sector_id' => $preferredSector->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_profile_update_cannot_change_email_directly(): void
    {
        $advertiser = User::factory()->create([
            'role' => UserRole::Advertiser,
            'email' => 'old@example.com',
            'email_verified_at' => now(),
            'password' => 'password123',
        ]);

        Sanctum::actingAs($advertiser);

        $response = $this->patchJson('/api/v1/auth/profile', [
            'display_name' => 'Company Display Name',
            'bio' => 'Company bio',
            'short_description' => 'Company short description',
            'email' => 'new@gmail.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail');

        $this->assertSame('old@example.com', $advertiser->fresh()->email);
    }

    public function test_user_can_change_email_through_old_and_new_email_verification_cycle(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
            'email' => 'old@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'change-email-device-1',
            'device_type' => 'android',
            'locale' => 'en',
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'change-email-device-2',
            'device_type' => 'ios',
            'locale' => 'ar',
        ]);

        $user->createToken('change-email-token-1');
        $user->createToken('change-email-token-2');

        Sanctum::actingAs($user);

        $requestCurrentResponse = $this->postJson('/api/v1/auth/email-change/request-current', [
            'current_password' => 'password123',
        ]);

        $requestCurrentResponse->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.current_email_change_code_sent'));

        $user = $user->fresh();
        $oldOtp = $user->email_change_old_otp;

        Notification::assertSentOnDemand(
            EmailOtpNotification::class,
            function (EmailOtpNotification $notification, array $channels, object $notifiable) use ($user): bool {
                return in_array('mail', $channels, true)
                    && ($notifiable->routes['mail'] ?? null) === $user->email;
            }
        );

        $verifyCurrentResponse = $this->postJson('/api/v1/auth/email-change/verify-current', [
            'otp' => $oldOtp,
        ]);

        $verifyCurrentResponse->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.current_email_verified_for_change'));

        $requestNewResponse = $this->postJson('/api/v1/auth/email-change/request-new', [
            'email' => 'new@gmail.com',
        ]);

        $requestNewResponse->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.new_email_change_code_sent'));

        $user = $user->fresh();
        $newOtp = $user->email_change_new_otp;

        Notification::assertSentOnDemand(
            EmailOtpNotification::class,
            function (EmailOtpNotification $notification, array $channels, object $notifiable): bool {
                return in_array('mail', $channels, true)
                    && ($notifiable->routes['mail'] ?? null) === 'new@gmail.com';
            }
        );

        $verifyNewResponse = $this->postJson('/api/v1/auth/email-change/verify-new', [
            'email' => 'new@gmail.com',
            'otp' => $newOtp,
        ]);

        $verifyNewResponse->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.email_changed_successfully_logged_out'));

        $user = $user->fresh();

        $this->assertSame('new@gmail.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_change_new_email);
        $this->assertNull($user->email_change_old_verified_at);
        $this->assertSame(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->count());
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'change-email-device-1',
        ]);
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'change-email-device-2',
        ]);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'oldpassword',
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'device-token-1',
            'device_type' => 'android',
            'locale' => 'en',
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'device-token-2',
            'device_type' => 'ios',
            'locale' => 'ar',
        ]);

        $user->createToken('device-session-1');
        $user->createToken('device-session-2');

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/auth/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.password_updated_logged_out_all_devices'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertSame(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->count());
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'device-token-1',
        ]);
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'device-token-2',
        ]);
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
            'password' => 'oldpassword',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/auth/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail');
    }

    public function test_investor_can_logout(): void
    {
        $investor = User::factory()->create([
            'role' => UserRole::Investor,
            'password' => 'password123',
        ]);
        $token = $investor->createToken('investor-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJsonPath('key', 'success');

        $this->assertSame(0, PersonalAccessToken::query()->count());
    }

    public function test_advertiser_can_logout(): void
    {
        $advertiser = User::factory()->create([
            'role' => UserRole::Advertiser,
            'password' => 'password123',
        ]);
        $token = $advertiser->createToken('advertiser-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJsonPath('key', 'success');

        $this->assertSame(0, PersonalAccessToken::query()->count());
    }
}
