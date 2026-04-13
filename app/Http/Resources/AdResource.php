<?php

namespace App\Http\Resources;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    protected bool $includeSectionB = false;

    public function includeSectionB(bool $includeSectionB = true): self
    {
        $this->includeSectionB = $includeSectionB;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum') ?? auth('sanctum')->user();
        $isOwner = $user?->id === $this->user_id;
        $currentLocale = app()->getLocale();

        $hasSeat = $user
            ? ($this->relationLoaded('investmentSeats') ? $this->investmentSeats->isNotEmpty() : false)
            : null;
        $hasSubmittedInterest = $user
            ? ($this->relationLoaded('interestRequests') ? $this->interestRequests->isNotEmpty() : false)
            : null;

        $status = $this->status?->value ?? $this->status;
        $seatPrice = $request->attributes->get(
            'seat_price',
            GeneralSetting::getValueForKey('seat_price')
        );
        $completedDealsCommission = GeneralSetting::getValueForKey('completed_deals_commission');
        $adminContactPhone = GeneralSetting::getValueForKey('contact_phone');
        $adminContactEmail = GeneralSetting::getValueForKey('contact_email');

        $canViewSectionB = $this->includeSectionB || $isOwner || ($hasSeat);
        $isFileOpen = (bool) $canViewSectionB;
        $canViewAdminContact = $hasSeat;

        return [
            'id' => $this->id,
            'opportunity_number' => $this->opportunity_number,
            'goal' => $this->goal?->value ?? $this->goal,
            'status' => OpportunityStatus::getFullObj($status, 'opportunity_status'),
            'image' => $this->image,
            'company_name' => $this->company_name,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'business_age_years' => $this->business_age_years,
            'investment_required' => $this->investment_required !== null ? (float) $this->investment_required : null,
            'business_stage' => $this->business_stage,
            'sale_percentage' => $this->sale_percentage !== null ? (float) $this->sale_percentage : null,
            'seat_price' => $seatPrice !== null ? (float) $seatPrice : null,
            'completed_deals_commission' => $completedDealsCommission !== null ? (float) $completedDealsCommission : null,
            'current_locale' => $currentLocale,
            'is_owner' => $isOwner,
            'file_access' => [
                'key' => $isFileOpen ? 'open' : 'locked',
                'label' => $isFileOpen ? __('apis.file_open') : __('apis.file_locked'),
                'is_open' => $isFileOpen,
            ],
            'has_seat' => $hasSeat,
            'can_buy_seat' => $user
                ? (! $isOwner && ! $hasSeat && $status !== OpportunityStatus::Reserved->value)
                : null,
            'can_submit_interest' => $user
                ? (! $isOwner && $hasSeat && ! $hasSubmittedInterest)
                : null,
            'has_submitted_interest' => $hasSubmittedInterest,
            $this->mergeWhen($canViewAdminContact, [
                'admin_contact_phone' => $adminContactPhone,
                'admin_contact_email' => $adminContactEmail,
            ]),
            $this->mergeWhen($canViewSectionB, [
                'legal_entity' => $this->legal_entity,
                'financial_status' => $this->financial_status,
                'investment_reason' => $this->investment_reason,
                'full_description' => $this->full_description,
            ]),
        ];
    }
}
