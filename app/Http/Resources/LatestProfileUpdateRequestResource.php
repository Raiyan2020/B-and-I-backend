<?php

namespace App\Http\Resources;

use App\Services\ProfileUpdateRequestService;
use Illuminate\Http\Resources\Json\JsonResource;

class LatestProfileUpdateRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        $service = app(ProfileUpdateRequestService::class);

        return [
            'id' => $this->id,
            'status' => [
                'key' => $this->status?->value,
                'label' => __('dashboard.'.($this->status?->value ?? 'pending')),
            ],
            'rejection_reason' => $this->rejection_reason,
            'submitted_at' => $this->created_at?->toDateTimeString(),
            'reviewed_at' => $this->reviewed_at?->toDateTimeString(),
            'updated_data' => $service->changedPayload($this->resource),
        ];
    }
}
