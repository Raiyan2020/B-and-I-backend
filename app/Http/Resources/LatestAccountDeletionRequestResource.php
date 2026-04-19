<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LatestAccountDeletionRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => [
                'key' => $this->status?->value,
                'label' => __('dashboard.'.($this->status?->value ?? 'pending')),
            ],
            'rejection_reason' => $this->rejection_reason,
            'submitted_at' => $this->created_at?->toDateTimeString(),
            'reviewed_at' => $this->reviewed_at?->toDateTimeString(),
        ];
    }
}
