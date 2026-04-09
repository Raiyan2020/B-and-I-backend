<?php

namespace App\Http\Resources;

use App\Services\Notifications\NotificationPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $service = app(NotificationPreferenceService::class);

        return [
            'settings' => $service->settingsFor($this->resource),
        ];
    }
}
