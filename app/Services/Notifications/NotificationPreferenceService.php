<?php

namespace App\Services\Notifications;

use App\Enums\NotificationCategory;
use App\Models\User;

class NotificationPreferenceService
{
    /**
     * @return array<int, array{key: string, label: string, enabled: bool}>
     */
    public function settingsFor(User $user): array
    {
        return array_map(
            fn (NotificationCategory $category) => [
                'key' => $category->value,
                'label' => __('enums.notification_category.'.$category->value),
                'enabled' => $this->isPushEnabled($user, $category),
            ],
            NotificationCategory::cases(),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        $mapped = [];

        foreach ($this->fieldMap() as $requestKey => $column) {
            if (array_key_exists($requestKey, $data)) {
                $mapped[$column] = (bool) $data[$requestKey];
            }
        }

        if (! empty($mapped)) {
            $user->update($mapped);
        }

        return $user->refresh();
    }

    public function isPushEnabled(User $user, NotificationCategory|string|null $category): bool
    {
        return match (NotificationCategory::normalize($category)) {
            NotificationCategory::Orders => (bool) $user->order_notifications_enabled,
            NotificationCategory::Interest => (bool) $user->interest_notifications_enabled,
            NotificationCategory::System => (bool) $user->system_notifications_enabled,
        };
    }

    /**
     * @return array<string, string>
     */
    private function fieldMap(): array
    {
        return [
            'orders' => 'order_notifications_enabled',
            'interest' => 'interest_notifications_enabled',
            'system' => 'system_notifications_enabled',
        ];
    }
}
