<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\FormatsOpportunityData;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    use FormatsOpportunityData;

    public function toArray(Request $request): array
    {
        $completedDealsCommission = GeneralSetting::getValueForKey('completed_deals_commission');

        return [
            'id' => $this->id,
            'opportunity_number' => $this->opportunity_number,
            'goal' => $this->goal?->value ?? $this->goal,
            'status' => $this->statusPayload(),
            'statuses' => $this->allStatusesPayload(),
            'completed_deals_commission' => $completedDealsCommission !== null
                ? (float) $completedDealsCommission
                : null,
            'review_note' => $this->review_note,
            'reviewed_at' => $this->reviewed_at?->toDateTimeString(),
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'owner_name' => $this->owner_name,
            'admin_company_name' => $this->admin_company_name,
            'license_number' => $this->license_number,
            'company_name' => $this->company_name,
            'category' => $this->categoryPayload(),
            'business_age_years' => $this->business_age_years,
            'investment_required' => (float) $this->investment_required,
            'business_stage' => $this->business_stage,
            'sale_percentage' => $this->sale_percentage !== null ? (float) $this->sale_percentage : null,
            'statistics' => $this->statisticsPayload(),
            'legal_entity' => $this->legal_entity,
            'financial_status' => $this->financial_status,
            'investment_reason' => $this->investment_reason,
            'full_description' => $this->full_description,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
