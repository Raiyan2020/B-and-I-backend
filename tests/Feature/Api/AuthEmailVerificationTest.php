<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Device;
use App\Models\PreferredSector;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_investor_registration_creates_unverified_user_and_sends_verification_email(): void
    {
        Notification::fake();

        $category = Category::factory()->create(['status' => true]);
        $preferredSector = PreferredSector::query()->create([
            'name' => ['ar' => 'تقنية', 'en' => 'Technology'],
            'status' => true,
        ]);

        $response = $this->withHeader('Accept-Language', 'en')->postJson('/api/v1/auth/register/investor', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'investor@gmail.com',
            'phone' => '45678901',
            'country_code' => '965',
            'password' => 'secret123',
            'investor_type' => 'angel',
            'capital' => 10000,
            'available_capital' => 10000,
            'preferred_sector_id' => $preferredSector->id,
            'category_id' => $category->id,
            'experience_level' => 10,
            'previous_investments_count' => 1,
            'investor_experience' => 'beginner',
            'agreed_to_terms' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('key', 'need_active')
            ->assertJsonPath('data.email', 'investor@gmail.com')
            ->assertJsonPath('data.email_verified', false)
            ->assertJsonPath('data.token', null);

        $user = User::query()->where('email', 'investor@gmail.com')->firstOrFail();

        self::assertNull($user->email_verified_at);
        self::assertSame('en', $user->lang);
        self::assertMatchesRegularExpression('/^\d{6}$/', (string) $user->otp);
        self::assertNotNull($user->otp_expires_at);

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_login_is_blocked_for_unverified_accounts_and_resends_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'role' => UserRole::Advertiser,
            'email' => 'company@example.com',
            'phone' => '45678901',
            'country_code' => '965',
            'lang' => 'ar',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'company@example.com',
            'password' => 'password',
        ]);

        $response->assertForbidden()
            ->assertJsonPath('key', 'need_active')
            ->assertJsonPath('msg', __('apis.email_verification_required'));

        self::assertMatchesRegularExpression('/^\d{6}$/', (string) $user->fresh()->otp);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_verified_user_can_login_with_email_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'verified-login@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'verified-login@example.com',
            'password' => 'password',
            'device_token' => 'user-web-token',
            'device_type' => 'web',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'verified-login@example.com');

        self::assertNotNull($response->json('data.token'));
        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'token' => 'user-web-token',
            'device_type' => 'web',
        ]);
    }

    public function test_verified_user_can_login_with_phone_country_code_and_password(): void
    {
        $user = User::factory()->create([
            'phone' => '45678901',
            'country_code' => '965',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'phone' => '45678901',
            'country_code' => '965',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.phone', '45678901')
            ->assertJsonPath('data.country_code', '965');

        self::assertNotNull($response->json('data.token'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'wrong-pass@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'wrong-pass@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.invalid_credentials'));
    }

    public function test_login_requires_email_or_phone(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.the_given_data_was_invalid'))
            ->assertJsonPath('response_status.validation_errors.email.0', 'The email field is required when phone is not present.')
            ->assertJsonPath('response_status.validation_errors.phone.0', 'The phone field is required when email is not present.');
    }

    public function test_verification_endpoint_marks_email_as_verified_and_logs_user_in(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'verify@example.com',
            'otp' => '123456',
            'otp_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->postJson('/api/v1/auth/email/verify', [
            'email' => 'verify@example.com',
            'password' => 'password',
            'otp' => '123456',
            'device_token' => 'verify-device-token',
            'device_type' => 'android',
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.email_verified_successfully'))
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email_verified', true);

        self::assertNotNull($user->fresh()->email_verified_at);
        self::assertNull($user->fresh()->email_verification_otp);
        self::assertNotNull($response->json('data.token'));
        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'token' => 'verify-device-token',
            'device_type' => 'android',
        ]);
    }

    public function test_resend_endpoint_returns_already_verified_message_for_verified_users(): void
    {
        Notification::fake();

        User::factory()->create([
            'role' => UserRole::Investor,
            'email' => 'verified@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/email/resend', [
            'email' => 'verified@example.com',
            'password' => 'password',
            'role' => UserRole::Investor->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.already_verified'));

        Notification::assertNothingSent();
    }

    public function test_resend_uses_the_user_locale_for_email_content(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'role' => UserRole::Investor,
            'email' => 'locale@example.com',
            'lang' => 'ar',
        ]);

        $response = $this->postJson('/api/v1/auth/email/resend', [
            'email' => 'locale@example.com',
            'password' => 'password',
            'role' => UserRole::Investor->value,
        ]);

        $response->assertOk();

        Notification::assertSentTo(
            $user,
            VerifyEmailNotification::class,
            function (VerifyEmailNotification $notification, array $channels, User $notifiable): bool {
                return $notification->toMail($notifiable)->subject === __('mail.verify_email.subject', locale: 'ar');
            }
        );
    }

    public function test_verification_endpoint_rejects_invalid_or_expired_otp(): void
    {
        User::factory()->unverified()->create([
            'email' => 'invalid-otp@example.com',
            'otp' => '654321',
            'otp_expires_at' => now()->subMinute(),
        ]);

        $response = $this->postJson('/api/v1/auth/email/verify', [
            'email' => 'invalid-otp@example.com',
            'password' => 'password',
            'otp' => '654321',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.verification_code_invalid'));
    }

    public function test_resend_does_not_send_verification_code_when_password_is_wrong(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'role' => UserRole::Investor,
            'email' => 'resend-check@example.com',
            'otp' => '111111',
            'otp_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->postJson('/api/v1/auth/email/resend', [
            'email' => 'resend-check@example.com',
            'password' => 'wrong-password',
            'role' => UserRole::Investor->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonPath('msg', __('apis.verification_email_sent'));

        $this->assertSame('111111', $user->fresh()->otp);
        Notification::assertNothingSent();
    }

    public function test_verification_endpoint_rejects_wrong_password_even_with_valid_otp(): void
    {
        User::factory()->unverified()->create([
            'email' => 'otp-password-check@example.com',
            'otp' => '222222',
            'otp_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->postJson('/api/v1/auth/email/verify', [
            'email' => 'otp-password-check@example.com',
            'password' => 'wrong-password',
            'otp' => '222222',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('key', 'fail')
            ->assertJsonPath('msg', __('apis.invalid_credentials'));
    }

    public function test_change_language_updates_current_device_locale_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'lang' => 'en',
        ]);

        Device::create([
            'user_id' => $user->id,
            'token' => 'current-device-token',
            'device_type' => 'android',
            'locale' => 'en',
        ]);

        $token = $user->createToken('test-device')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/general/change-lang', [
            'lang' => 'ar',
            'device_token' => 'current-device-token',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.lang', 'ar');

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'token' => 'current-device-token',
            'locale' => 'ar',
        ]);
        $this->assertSame('ar', $user->fresh()->lang);
    }
}
