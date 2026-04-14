<?php

namespace App\Http\Resources;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Resources\Concerns\FormatsOpportunityData;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdLightResource extends JsonResource
{
    use FormatsOpportunityData;

    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum') ?? auth('sanctum')->user();
        $isInvestor = $user?->role === UserRole::Investor;
        $isOwner = $user?->id === $this->user_id;
        $hasSeat = $user
            ? ($this->relationLoaded('investmentSeats') ? $this->investmentSeats->isNotEmpty() : false)
            : null;
        $hasSubmittedInterest = $user
            ? ($this->relationLoaded('interestRequests') ? $this->interestRequests->isNotEmpty() : false)
            : null;
        $status = $this->status?->value ?? $this->status;
        $goal = $this->goal?->value ?? $this->goal;
        $seatPrice = $request->attributes->get(
            'seat_price',
            GeneralSetting::getValueForKey('seat_price')
        );

        return [
            'id' => $this->id,
            'opportunity_number' => $this->opportunity_number,
            'company_name' => $this->company_name,
            'image' => $this->image,
            'goal' => $goal ? [
                'key' => $goal,
                'label' => __("dashboard.goal_{$goal}"),
            ] : null,
            'status' => OpportunityStatus::getFullObj($status, 'opportunity_status'),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'investment_required' => $this->investment_required !== null ? (float) $this->investment_required : null,
            'sale_percentage' => $this->sale_percentage !== null ? (float) $this->sale_percentage : null,
            'seat_price' => $seatPrice !== null ? (float) $seatPrice : null,
            'statistics' => $this->statisticsPayload(),
            'is_owner' => $isOwner,
            'has_seat' => $hasSeat,
            'can_buy_seat' => $user
                ? (! $isOwner && $isInvestor && ! $hasSeat && $status !== OpportunityStatus::Reserved->value)
                : null,
            'can_submit_interest' => $user
                ? (! $isOwner && $isInvestor && $hasSeat && ! $hasSubmittedInterest)
                : null,
            'has_submitted_interest' => $hasSubmittedInterest,
        ];
    }
}
