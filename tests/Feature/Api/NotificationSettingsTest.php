<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_notification_settings(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Investor,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/notification-settings');

        $response->assertOk()
            ->assertJsonCount(3, 'data.settings')
            ->assertJsonPath('data.settings.0.key', 'orders');
    }

    public function test_authenticated_user_can_update_notification_settings(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
            'order_notifications_enabled' => true,
            'interest_notifications_enabled' => true,
            'system_notifications_enabled' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/auth/notification-settings', [
            'orders' => false,
            'interest' => true,
            'system' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.settings.0.enabled', false)
            ->assertJsonPath('data.settings.1.enabled', true)
            ->assertJsonPath('data.settings.2.enabled', false);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'order_notifications_enabled' => false,
            'interest_notifications_enabled' => true,
            'system_notifications_enabled' => false,
        ]);
    }
}
