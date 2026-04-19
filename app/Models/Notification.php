<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends DatabaseNotification
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'title',
        'body',
        'notification_type',
        'payload',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function getTitleAttribute(): string
    {
        return $this->resolveLocalizedText('title');
    }

    public function getBodyAttribute(): string
    {
        return $this->resolveLocalizedText('body');
    }

    public function getNotificationTypeAttribute(): string
    {
        return NotificationType::normalize(
            data_get($this->data, 'notification_type', NotificationType::Generic->value)
        )->value;
    }

    public function getPayloadAttribute(): array
    {
        $data = $this->data ?? [];

        unset(
            $data['notification_type'],
            $data['notification_category'],
            $data['title'],
            $data['body'],
            $data['click_action'],
            $data['icon'],
            $data['model_type'],
            $data['model_id'],
            $data['model_data']
        );

        return $data;
    }

    public function targetUrl(): ?string
    {
        if ($this->notifiable_type !== Admin::class) {
            return null;
        }

        $modelType = data_get($this->data, 'model_type');
        $modelId = data_get($this->data, 'model_id');

        if ($orderId = data_get($this->data, 'order_id')) {
            return route('admin.orders.show', $orderId);
        }

        if (! $modelType || ! $modelId) {
            return null;
        }

        return match ($modelType) {
            'User' => route('admin.users.show', $modelId),
            'Opportunity' => route('admin.opportunities.show', $modelId),
            'InvestmentSeat' => route('admin.investment-seats.show', $modelId),
            'InterestRequest' => route('admin.interest-requests.show', $modelId),
            'ProfileUpdateRequest' => route('admin.profile-update-requests.show', $modelId),
            'AccountDeletionRequest' => route('admin.account-deletion-requests.show', $modelId),
            'CompanyInvestorInterestRequest' => route('admin.company-investor-interest-requests.index', [
                'company_id' => data_get($this->data, 'company_id'),
                'investor_id' => data_get($this->data, 'investor_id'),
            ]),
            default => null,
        };
    }

    private function resolveLocalizedText(string $key): string
    {
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $rawOverride = data_get($this->data, $key);
        $override = is_array($rawOverride)
            ? data_get($rawOverride, $locale)
            : $rawOverride;

        if (filled($override)) {
            return (string) $override;
        }

        $type = $this->notification_type;
        $translationKey = "notifications.{$type}_{$key}";
        $translated = trans($translationKey, $this->translationReplacements(), $locale);

        if ($translated !== $translationKey) {
            return $translated;
        }

        return '';
    }

    private function translationReplacements(): array
    {
        $data = $this->data ?? [];
        $replacements = [];

        foreach ($data as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $replacements[$key] = (string) $value;
            }
        }

        return $replacements;
    }
}
