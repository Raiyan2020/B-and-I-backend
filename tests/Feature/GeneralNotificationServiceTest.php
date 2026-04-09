<?php

namespace Tests\Feature;

use App\Enums\NotificationCategory;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\Notifications\FirebaseNotificationService;
use App\Services\Notifications\GeneralNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GeneralNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_stores_notification_and_sends_push_when_category_is_enabled(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Investor,
            'order_notifications_enabled' => true,
        ]);

        $firebase = Mockery::mock(FirebaseNotificationService::class);
        $firebase->shouldReceive('sendToUser')
            ->once()
            ->andReturn(['sent' => 1, 'failed' => 0, 'responses' => []]);
        $this->app->instance(FirebaseNotificationService::class, $firebase);

        $service = $this->app->make(GeneralNotificationService::class);

        $stored = $service->sendToUser(
            $user,
            new GeneralNotification(
                title: ['ar' => 'طلب جديد', 'en' => 'New order'],
                body: ['ar' => 'تم استلام طلب جديد', 'en' => 'A new order was received'],
                notificationType: 'order_created',
                category: NotificationCategory::Orders,
                payload: [
                    'model_type' => 'Order',
                    'model_id' => 15,
                ],
            )
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $stored->id,
            'user_id' => $user->id,
            'notification_category' => NotificationCategory::Orders->value,
            'notification_type' => 'order_created',
            'model_type' => 'Order',
            'model_id' => 15,
        ]);
    }

    public function test_it_stores_notification_without_push_when_category_is_disabled(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Advertiser,
            'interest_notifications_enabled' => false,
        ]);

        $firebase = Mockery::mock(FirebaseNotificationService::class);
        $firebase->shouldNotReceive('sendToUser');
        $this->app->instance(FirebaseNotificationService::class, $firebase);

        $service = $this->app->make(GeneralNotificationService::class);

        $stored = $service->sendToUser(
            $user,
            new GeneralNotification(
                title: ['ar' => 'اهتمام جديد', 'en' => 'New interest'],
                body: ['ar' => 'يوجد اهتمام جديد بإعلانك', 'en' => 'There is new interest in your advertisement'],
                notificationType: 'interest_received',
                category: NotificationCategory::Interest,
                payload: [
                    'model_type' => 'Opportunity',
                    'model_id' => 7,
                ],
            )
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $stored->id,
            'user_id' => $user->id,
            'notification_category' => NotificationCategory::Interest->value,
            'notification_type' => 'interest_received',
            'model_type' => 'Opportunity',
            'model_id' => 7,
        ]);
    }
}
