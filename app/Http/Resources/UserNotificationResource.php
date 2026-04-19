<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'seen' => $this->read_at !== null,
            'read_at' => optional($this->read_at)->toISOString(),
            'notification_category' => $this->notification_category,
            'notification_type' => $this->notification_type,
            'model_type' => data_get($this->data, 'model_type'),
            'model_id' => data_get($this->data, 'model_id'),
            'payload' => $this->payload ?? [],
            'created_at' => optional($this->created_at)->toISOString(),
            'target_url' => $this->targetUrl(),
        ];
    }
}
