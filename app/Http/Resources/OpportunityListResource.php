<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\FormatsOpportunityData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityListResource extends JsonResource
{
    use FormatsOpportunityData;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'opportunity_number' => $this->opportunity_number,
            'company_name' => $this->company_name,
            'goal' => $this->goal?->value ?? $this->goal,
            'status' => $this->statusPayload(),
            'category' => $this->categoryPayload(),
            'investment_required' => (float) $this->investment_required,
            'sale_percentage' => $this->sale_percentage !== null ? (float) $this->sale_percentage : null,
            'created_at' => $this->created_at?->toDateTimeString(),
            'created_at_formatted' => $this->created_at
                ? $this->created_at->locale(app()->getLocale())->translatedFormat('d M Y - h:i A')
                : null,
        ];
    }
}
