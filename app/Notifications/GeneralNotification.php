<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
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
        public readonly array $title,
        public readonly array $body,
        public readonly string $notificationType,
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

    /**
     * @return array{title: array{ar: string, en: string}, body: array{ar: string, en: string}, click_action: ?string, icon: ?string}
     */
    public function message(): array
    {
        return [
            'title' => [
                'ar' => $this->title['ar'] ?? $this->title['en'] ?? '',
                'en' => $this->title['en'] ?? $this->title['ar'] ?? '',
            ],
            'body' => [
                'ar' => $this->body['ar'] ?? $this->body['en'] ?? '',
                'en' => $this->body['en'] ?? $this->body['ar'] ?? '',
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
        return [
            'user_id' => $user->id,
            'admin_id' => null,
            'title_ar' => $this->message()['title']['ar'],
            'title_en' => $this->message()['title']['en'],
            'body_ar' => $this->message()['body']['ar'],
            'body_en' => $this->message()['body']['en'],
            'notification_category' => $this->category()->value,
            'notification_type' => $this->notificationType,
            'model_type' => $this->resolveModelType(),
            'model_id' => $this->resolveModelId(),
            'payload' => $this->databasePayload(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function databaseAttributesForAdmin(Admin $admin): array
    {
        return [
            'user_id' => null,
            'admin_id' => $admin->id,
            'title_ar' => $this->message()['title']['ar'],
            'title_en' => $this->message()['title']['en'],
            'body_ar' => $this->message()['body']['ar'],
            'body_en' => $this->message()['body']['en'],
            'notification_category' => $this->category()->value,
            'notification_type' => $this->notificationType,
            'model_type' => $this->resolveModelType(),
            'model_id' => $this->resolveModelId(),
            'payload' => $this->databasePayload(),
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
        $payload = $this->databasePayload();

        return array_map(
            static fn ($value) => $value === null ? '' : (string) $value,
            array_filter([
                'notification_id' => $notification->id,
                $notifiableKey => $notifiableId,
                'category' => $this->category()->value,
                'notification_type' => $this->notificationType,
                'model_type' => $this->resolveModelType(),
                'model_id' => $this->resolveModelId(),
                'request_num' => $payload['request_num'] ?? null,
            ] + $payload, static fn ($value) => $value !== null && ! is_array($value))
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function databasePayload(): array
    {
        return array_filter([
            'request_num' => $this->model?->request_num ?? $this->model?->order_num ?? $this->model?->complaint_num ?? null,
        ] + $this->payload, static fn ($value) => $value !== null);
    }

    private function resolveModelType(): ?string
    {
        if ($this->model !== null) {
            return class_basename($this->model);
        }

        return $this->payload['model_type'] ?? null;
    }

    private function resolveModelId(): ?int
    {
        return $this->model?->getKey() ?? (isset($this->payload['model_id']) ? (int) $this->payload['model_id'] : null);
    }
}
