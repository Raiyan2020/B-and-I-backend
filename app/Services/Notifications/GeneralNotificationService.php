<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GeneralNotificationService
{
    public function __construct(
        private readonly FirebaseNotificationService $firebaseNotificationService,
        private readonly NotificationPreferenceService $notificationPreferenceService,
    ) {}

    public function sendToUser(User $user, GeneralNotification $notification): Notification
    {
        $stored = $user->notifications()->create(
            $notification->databaseAttributesFor($user)
        );
        // Firebase Admin Dashboard Setup
        if ($this->notificationPreferenceService->isPushEnabled($user, $notification->category())) {
            $this->dispatchUserPushSafely($user, $notification, $stored);
        }

        return $stored;
    }

    public function sendToAdmin(Admin $admin, GeneralNotification $notification): Notification
    {
        $stored = $admin->notifications()->create(
            $notification->databaseAttributesForAdmin($admin)
        );
        // Firebase Admin Dashboard Setup
        $this->dispatchAdminPushSafely($admin, $notification, $stored);

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

    /**
     * @param  iterable<Admin>  $admins
     * @return Collection<int, Notification>
     */
    public function sendToAdmins(iterable $admins, GeneralNotification $notification): Collection
    {
        $stored = collect();

        foreach ($admins as $admin) {
            if (! $admin instanceof Admin) {
                continue;
            }

            $stored->push($this->sendToAdmin($admin, $notification));
        }

        return $stored;
    }
    // Firebase Admin Dashboard Setup
    private function dispatchUserPushSafely(User $user, GeneralNotification $notification, Notification $stored): void
    {
        try {
            $result = $this->firebaseNotificationService->sendToUser(
                $user,
                $notification->message(),
                data: $notification->pushData($stored, $user),
            );

            Log::info('Firebase user push dispatch result', [
                'user_id' => $user->id,
                'notification_id' => $stored->id,
                'category' => $notification->category()?->value ?? (string) $notification->category(),
                'result' => $result,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function dispatchAdminPushSafely(Admin $admin, GeneralNotification $notification, Notification $stored): void
    {
        try {
            $result = $this->firebaseNotificationService->sendToAdmin(
                $admin,
                $notification->message(),
                data: $notification->pushDataForAdmin($stored, $admin),
            );

            Log::info('Firebase admin push dispatch result', [
                'admin_id' => $admin->id,
                'notification_id' => $stored->id,
                'category' => $notification->category()?->value ?? (string) $notification->category(),
                'result' => $result,
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
    //End Firebase Admin Dashboard Setup
}
