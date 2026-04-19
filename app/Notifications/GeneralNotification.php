<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationType;
use App\Models\Admin;
use App\Models\Notification as NotificationModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GeneralNotification
{
    /**
     * @param  array{ar?: string, en?: string}  $title
     * @param  array{ar?: string, en?: string}  $body
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly array $title = [],
        public readonly array $body = [],
        public readonly NotificationType|string $notificationType = NotificationType::Generic,
        public readonly NotificationCategory|string|null $category = null,
        public readonly ?Model $model = null,
        public readonly array $payload = [],
        public readonly ?string $clickAction = null,
        public readonly ?string $icon = null,
    ) {}

    public function category(): NotificationCategory
    {
        return NotificationCategory::normalize($this->category);
    }

    public function notificationType(): NotificationType
    {
        return NotificationType::normalize($this->notificationType);
    }

    /**
     * @return array{title: array{ar: string, en: string}, body: array{ar: string, en: string}, click_action: ?string, icon: ?string}
     */
    public function message(): array
    {
        return [
            'title' => [
                'ar' => $this->resolveText('title', 'ar'),
                'en' => $this->resolveText('title', 'en'),
            ],
            'body' => [
                'ar' => $this->resolveText('body', 'ar'),
                'en' => $this->resolveText('body', 'en'),
            ],
            'click_action' => $this->clickAction,
            'icon' => $this->icon,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function databaseAttributesFor(User $user): array
    {
        return $this->databaseAttributesForNotifiable($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function databaseAttributesForAdmin(Admin $admin): array
    {
        return $this->databaseAttributesForNotifiable($admin);
    }

    /**
     * @return array<string, mixed>
     */
    public function databaseAttributesForNotifiable(User|Admin $notifiable): array
    {
        return [
            'type' => static::class,
            'notification_category' => $this->category()->value,
            'data' => $this->databasePayload(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function pushData(NotificationModel $notification, User $user): array
    {
        return $this->pushDataForNotifiable($notification, 'user_id', $user->id);
    }

    /**
     * @return array<string, string>
     */
    public function pushDataForAdmin(NotificationModel $notification, Admin $admin): array
    {
        return $this->pushDataForNotifiable($notification, 'admin_id', $admin->id);
    }

    /**
     * @return array<string, string>
     */
    private function pushDataForNotifiable(NotificationModel $notification, string $notifiableKey, int $notifiableId): array
    {
        $payload = $notification->data ?? $this->databasePayload();

        return array_map(
            static fn ($value) => $value === null ? '' : (string) $value,
            array_filter([
                'notification_id' => $notification->id,
                $notifiableKey => $notifiableId,
                'category' => $notification->notification_category,
                'notification_type' => $notification->notification_type,
                'model_type' => data_get($payload, 'model_type'),
                'model_id' => data_get($payload, 'model_id'),
                'request_num' => data_get($payload, 'request_num'),
            ] + $payload, static fn ($value) => $value !== null && ! is_array($value))
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function databasePayload(): array
    {
        return array_filter(array_replace([
            'notification_type' => $this->notificationType()->value,
            'notification_category' => $this->category()->value,
            'title' => $this->normalizedLocalizedValue($this->title),
            'body' => $this->normalizedLocalizedValue($this->body),
            'click_action' => $this->clickAction,
            'icon' => $this->icon,
            'model_type' => $this->resolveModelType(),
            'model_id' => $this->resolveModelId(),
            'model_data' => $this->model?->toArray(),
            'request_num' => $this->model?->request_num ?? $this->model?->order_num ?? $this->model?->complaint_num ?? null,
        ], $this->payload), static fn ($value) => $value !== null && $value !== []);
    }

    /**
     * @param  array{ar?: string, en?: string}  $value
     * @return array<string, string>
     */
    private function normalizedLocalizedValue(array $value): array
    {
        return array_filter([
            'ar' => $value['ar'] ?? null,
            'en' => $value['en'] ?? null,
        ], static fn ($item) => filled($item));
    }

    private function resolveText(string $key, string $locale): string
    {
        $override = data_get($this->{$key}, $locale);

        if (filled($override)) {
            return (string) $override;
        }

        $translationKey = "notifications.{$this->notificationType()->value}_{$key}";
        $translated = trans($translationKey, $this->translationReplacements(), $locale);

        return $translated === $translationKey ? '' : (string) $translated;
    }

    /**
     * @return array<string, mixed>
     */
    private function translationReplacements(): array
    {
        return array_filter($this->payload + [
            'request_num' => $this->model?->request_num ?? $this->model?->order_num ?? $this->model?->complaint_num ?? null,
            'model_id' => $this->resolveModelId(),
        ], static fn ($value) => is_scalar($value) || $value === null);
    }

    private function resolveModelType(): ?string
    {
        if ($this->model !== null) {
            return class_basename($this->model);
        }

        return isset($this->payload['model_type']) ? (string) $this->payload['model_type'] : null;
    }

    private function resolveModelId(): ?int
    {
        return $this->model?->getKey() ?? (isset($this->payload['model_id']) ? (int) $this->payload['model_id'] : null);
    }
}
