<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'opportunities_count' => $this->opportunities_count ?? 0,
            'image' => $this->image,
        ];
    }
}
