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
        $isFileOpen = (bool) ($isOwner || $hasSeat);
        $canBuySeat = $user
            ? (
                ! $isOwner
                && $isInvestor
                && ! $hasSeat
                && $status === OpportunityStatus::Published->value
            )
            : null;
        $canSubmitInterest = $user
            ? (
                ! $isOwner
                && $isInvestor
                && $hasSeat
                && ! $hasSubmittedInterest
                && in_array($status, [
                    OpportunityStatus::Published->value,
                    OpportunityStatus::Reserved->value,
                ], true)
            )
            : null;

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
            'file_access' => [
                'key' => $isFileOpen ? 'open' : 'locked',
                'label' => $isFileOpen ? __('apis.file_open') : __('apis.file_locked'),
                'is_open' => $isFileOpen,
            ],
            'is_owner' => $isOwner,
            'has_seat' => $hasSeat,
            'can_buy_seat' => $canBuySeat,
            'can_submit_interest' => $canSubmitInterest,
            'has_submitted_interest' => $hasSubmittedInterest,
        ];
    }
}
