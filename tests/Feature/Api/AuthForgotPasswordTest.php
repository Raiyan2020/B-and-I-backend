<?php

namespace Tests\Feature\Api;

use App\Models\AuthUpdate;
use App\Models\Device;
use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_request_sends_otp_and_creates_auth_update(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'forgot@example.com',
            'lang' => 'en',
        ]);

        $response = $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'forgot@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.forgot_password_code_sent'));

        $authUpdate = AuthUpdate::query()
            ->where('auth_updateable_type', User::class)
            ->where('auth_updateable_id', $user->id)
            ->where('type', 'password')
            ->where('sub_type', 'forgot_password')
            ->where('attribute', 'forgot@example.com')
            ->first();

        $this->assertNotNull($authUpdate);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $authUpdate->code);
        $this->assertNull($authUpdate->verified_at);

        Notification::assertSentOnDemand(
            EmailOtpNotification::class,
            function (EmailOtpNotification $notification, array $channels, object $notifiable) use ($user): bool {
                return in_array('mail', $channels, true)
                    && ($notifiable->routes['mail'] ?? null) === $user->email;
            }
        );
    }

    public function test_forgot_password_request_returns_not_found_for_unknown_email(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'missing@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.account_not_found'));

        $this->assertSame(0, AuthUpdate::query()->count());
        Notification::assertNothingSent();
    }

    public function test_forgot_password_verify_marks_auth_update_as_verified(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'verify-forgot@example.com',
        ]);

        $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'verify-forgot@example.com',
        ])->assertOk();

        $authUpdate = AuthUpdate::query()
            ->where('type', 'password')
            ->where('sub_type', 'forgot_password')
            ->where('attribute', 'verify-forgot@example.com')
            ->firstOrFail();

        $response = $this->postJson('/api/v1/auth/password/forgot/verify-code', [
            'email' => 'verify-forgot@example.com',
            'otp' => $authUpdate->code,
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.forgot_password_code_verified'));

        $authUpdate->refresh();

        $this->assertNull($authUpdate->code);
        $this->assertNull($authUpdate->code_expires_at);
        $this->assertNotNull($authUpdate->verified_at);
    }

    public function test_resending_forgot_password_code_updates_existing_auth_update_record(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'resend-forgot@example.com',
        ]);

        $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'resend-forgot@example.com',
        ])->assertOk();

        $firstAuthUpdate = AuthUpdate::query()
            ->where('type', 'password')
            ->where('sub_type', 'forgot_password')
            ->where('attribute', 'resend-forgot@example.com')
            ->firstOrFail();

        $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'resend-forgot@example.com',
        ])->assertOk();

        $this->assertSame(1, AuthUpdate::query()
            ->where('type', 'password')
            ->where('sub_type', 'forgot_password')
            ->where('attribute', 'resend-forgot@example.com')
            ->count());

        $this->assertSame($firstAuthUpdate->id, AuthUpdate::query()
            ->where('type', 'password')
            ->where('sub_type', 'forgot_password')
            ->where('attribute', 'resend-forgot@example.com')
            ->firstOrFail()
            ->id);
    }

    public function test_user_can_reset_forgotten_password_after_verifying_code(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset-forgot@example.com',
            'password' => 'oldpassword',
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'forgot-password-device-1',
            'device_type' => 'android',
            'locale' => 'en',
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'forgot-password-device-2',
            'device_type' => 'ios',
            'locale' => 'ar',
        ]);

        $user->createToken('forgot-password-token-1');
        $user->createToken('forgot-password-token-2');

        $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'reset-forgot@example.com',
        ])->assertOk();

        $authUpdate = AuthUpdate::query()
            ->where('type', 'password')
            ->where('sub_type', 'forgot_password')
            ->where('attribute', 'reset-forgot@example.com')
            ->firstOrFail();

        $this->postJson('/api/v1/auth/password/forgot/verify-code', [
            'email' => 'reset-forgot@example.com',
            'otp' => $authUpdate->code,
        ])->assertOk();

        $response = $this->postJson('/api/v1/auth/password/forgot/reset', [
            'email' => 'reset-forgot@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.password_reset_successfully_login_again'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertSame(0, AuthUpdate::query()
            ->where('auth_updateable_type', User::class)
            ->where('auth_updateable_id', $user->id)
            ->where('type', 'password')
            ->count());
        $this->assertSame(0, PersonalAccessToken::query()->where('tokenable_id', $user->id)->count());
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'forgot-password-device-1',
        ]);
        $this->assertDatabaseMissing('devices', [
            'user_id' => $user->id,
            'token' => 'forgot-password-device-2',
        ]);
    }

    public function test_reset_forgotten_password_requires_verified_code(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'requires-verify@example.com',
        ]);

        $this->postJson('/api/v1/auth/password/forgot/request-code', [
            'email' => 'requires-verify@example.com',
        ])->assertOk();

        $response = $this->postJson('/api/v1/auth/password/forgot/reset', [
            'email' => 'requires-verify@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.forgot_password_verification_required'));
    }
}
