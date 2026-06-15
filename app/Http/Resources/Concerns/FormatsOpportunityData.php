<?php

namespace App\Http\Resources\Concerns;

use App\Enums\OpportunityStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait FormatsOpportunityData
{
    protected function statisticsPayload(): array
    {
        return [
            'purchased_seats_count' => (int) ($this->investment_seats_count ?? 0),
            'interest_requests_count' => (int) ($this->interest_requests_count ?? 0),
            'views_count' => (int) ($this->views_count ?? 0),
        ];
    }

    protected function categoryPayload(): ?array
    {
        if (! $this->category) {
            return null;
        }

        return [
            'id' => $this->category->id,
            'name' => $this->category->name,
        ];
    }

    protected function statusPayload(OpportunityStatus|string|null $status = null): ?array
    {
        $statusValue = $status instanceof OpportunityStatus ? $status->value : $status;
        $statusValue ??= $this->status?->value ?? $this->status;

        if (! $statusValue) {
            return null;
        }

        return [
            'key' => $statusValue,
            'label' => __("dashboard.opportunity_status_{$statusValue}"),
            'color' => match ($statusValue) {
                OpportunityStatus::Pending->value => 'warning',
                OpportunityStatus::NeedsRevision->value => 'danger',
                OpportunityStatus::Published->value => 'success',
                OpportunityStatus::Reserved->value => 'info',
                OpportunityStatus::Completed->value => 'secondary',
                default => 'secondary',
            },
            'is_current' => ($this->status?->value ?? $this->status) === $statusValue,
        ];
    }

    protected function allStatusesPayload(): array
    {
        return Collection::make(OpportunityStatus::cases())
            ->map(fn (OpportunityStatus $status) => $this->statusPayload($status))
            ->values()
            ->all();
    }

    protected function localizedOptionPayload(?string $value, string $translationGroup): ?array
    {
        if (blank($value)) {
            return null;
        }

        $candidates = array_unique(array_filter([
            $value,
            Str::snake($value),
            Str::snake(Str::lower($value)),
        ]));

        foreach ($candidates as $candidate) {
            $translationKey = "enums.{$translationGroup}.{$candidate}";
            $label = __($translationKey);

            if ($label !== $translationKey) {
                return [
                    'key' => $value,
                    'label' => $label,
                ];
            }
        }

        return [
            'key' => $value,
            'label' => $value,
        ];
    }
}
