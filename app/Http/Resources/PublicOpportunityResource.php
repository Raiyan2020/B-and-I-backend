<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicOpportunityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'goal' => $this->goal?->value ?? $this->goal,
            'company_name' => $this->company_name,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'business_age_years' => $this->business_age_years,
            'investment_required' => (float) $this->investment_required,
            'business_stage' => $this->business_stage,
            'sale_percentage' => $this->sale_percentage !== null ? (float) $this->sale_percentage : null,
            'legal_entity' => $this->legal_entity,
            'financial_status' => $this->financial_status,
            'investment_reason' => $this->investment_reason,
            'full_description' => $this->full_description,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
