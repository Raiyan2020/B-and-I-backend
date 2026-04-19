<?php

namespace App\Jobs;

use App\Enums\NotificationCategory;
use App\Enums\NotificationType;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\Notifications\GeneralNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPlatformNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public readonly array $title,
        public readonly array $body,
        public readonly string $sendTo,
        public readonly int $actorAdminId,
    ) {
        $this->onQueue('platform-notifications');
    }

    public function handle(GeneralNotificationService $notificationService): void
    {
        Log::info('Processing platform notification job', [
            'send_to' => $this->sendTo,
            'actor_admin_id' => $this->actorAdminId,
        ]);

        $notification = new GeneralNotification(
            title: $this->title,
            body: $this->body,
            notificationType: NotificationType::AdminNotification,
            category: NotificationCategory::System,
            payload: [
                'sender_admin_id' => $this->actorAdminId,
            ],
        );

        match ($this->sendTo) {
            'admins' => $notificationService->sendToAdmins(
                Admin::query()
                    ->whereKeyNot($this->actorAdminId)
                    ->where('is_blocked', false)
                    ->get(),
                $notification,
            ),
            'investors' => $notificationService->sendToUsers(
                User::query()
                    ->where('role', UserRole::Investor->value)
                    ->where('is_active', true)
                    ->where('is_blocked', false)
                    ->get(),
                $notification,
            ),
            'advertisers' => $notificationService->sendToUsers(
                User::query()
                    ->where('role', UserRole::Advertiser->value)
                    ->where('is_active', true)
                    ->where('is_blocked', false)
                    ->get(),
                $notification,
            ),
            default => null,
        };
    }
}
