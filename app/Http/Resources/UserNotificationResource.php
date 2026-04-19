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
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'body_ar' => $this->body_ar,
            'body_en' => $this->body_en,
            'seen' => (bool) $this->seen,
            'notification_category' => $this->notification_category,
            'notification_type' => $this->notification_type,
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'payload' => $this->payload ?? [],
            'created_at' => optional($this->created_at)->toISOString(),
            'target_url' => $this->targetUrl(),
        ];
    }
}
