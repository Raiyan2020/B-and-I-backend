<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Collection;

class GeneralNotificationService
{
    public function __construct(
        private readonly FirebaseNotificationService $firebaseNotificationService,
        private readonly NotificationPreferenceService $notificationPreferenceService,
    ) {}

    public function sendToUser(User $user, GeneralNotification $notification): Notification
    {
        $stored = Notification::query()->create(
            $notification->databaseAttributesFor($user)
        );

        if ($this->notificationPreferenceService->isPushEnabled($user, $notification->category())) {
            $this->firebaseNotificationService->sendToUser(
                $user,
                $notification->message(),
                data: $notification->pushData($stored, $user),
            );
        }

        return $stored;
    }

    /**
     * @param  iterable<User>  $users
     * @return Collection<int, Notification>
     */
    public function sendToUsers(iterable $users, GeneralNotification $notification): Collection
    {
        $stored = collect();

        foreach ($users as $user) {
            if (! $user instanceof User) {
                continue;
            }

            $stored->push($this->sendToUser($user, $notification));
        }

        return $stored;
    }
}
