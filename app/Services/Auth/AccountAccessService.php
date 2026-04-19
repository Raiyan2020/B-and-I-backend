<?php

namespace App\Services\Auth;

use App\Enums\NotificationCategory;
use App\Enums\NotificationType;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\Devices\DeviceService;
use App\Services\Notifications\GeneralNotificationService;

class AccountAccessService
{
    public function __construct(
        private readonly GeneralNotificationService $notifications,
        private readonly DeviceService $deviceService,
    ) {}

    public function blockUser(User $user, ?Admin $actor = null): void
    {
        $this->notifications->sendToUser($user, new GeneralNotification(
            notificationType: NotificationType::UserBlocked,
            category: NotificationCategory::System,
            payload: [
                'force_logout' => true,
                'actor_admin_id' => $actor?->id,
                'actor_name' => $actor?->name,
            ],
        ));

        $this->revokeUserAccess($user);
    }

    public function deactivateUser(User $user, ?Admin $actor = null): void
    {
        $this->notifications->sendToUser($user, new GeneralNotification(
            notificationType: NotificationType::UserDeactivated,
            category: NotificationCategory::System,
            payload: [
                'force_logout' => true,
                'actor_admin_id' => $actor?->id,
                'actor_name' => $actor?->name,
            ],
        ));

        $this->revokeUserAccess($user);
    }

    public function blockAdmin(Admin $admin, ?Admin $actor = null): void
    {
        $this->notifications->sendToAdmin($admin, new GeneralNotification(
            notificationType: NotificationType::AdminBlocked,
            category: NotificationCategory::System,
            payload: [
                'force_logout' => true,
                'actor_admin_id' => $actor?->id,
                'actor_name' => $actor?->name,
            ],
        ));

        $this->revokeAdminAccess($admin);
    }

    public function revokeUserAccess(User $user): void
    {
        $user->tokens()->delete();
        $this->deviceService->forgetAllUserDevices($user);
    }

    public function revokeAdminAccess(Admin $admin): void
    {
        $this->deviceService->forgetAllAdminDevices($admin);
    }
}
