<?php

namespace App\Http\Resources;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Services\Notifications\NotificationPreferenceService;
use BackedEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $token;

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function toArray($request): array
    {
        $roleValue = $this->role->value;

        $data = [
            'id' => $this->id,
            'user_name' => $this->formattedUserName(),
            'role' => [
                'key' => $roleValue,
                'label' => UserRole::getTranslatedName($roleValue, 'user_role'),
            ],
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'display_name' => $this->display_name ?: $this->name,
            'image' => $this->image,
            'bio' => $this->bio,
            'short_description' => $this->short_description,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'phone' => $this->phone,
            'email_verified' => (bool) $this->email_verified_at,
            'lang' => $this->lang,
            'notification_settings' => app(NotificationPreferenceService::class)->settingsFor($this->resource),
            'token' => $this->when($this->token ?? null, $this->token),
        ];

        if ($this->role === UserRole::Investor) {
            if (! isset($this->purchased_seats_count) || ! isset($this->successful_investments_count)) {
                $this->resource->loadCount([
                    'investmentSeats as purchased_seats_count',
                    'awardedOpportunities as successful_investments_count' => fn ($query) => $query
                        ->where('status', OpportunityStatus::Completed->value),
                ]);
            }

            $experience = $this->investor_experience;
            $experienceValue = $experience instanceof BackedEnum ? $experience->value : $experience;
            $investorType = $this->investor_type;
            $investorTypeValue = $investorType instanceof BackedEnum ? $investorType->value : $investorType;
            $preferredSectorId = $this->preferred_sector_id;
            $categoryId = $this->category_id;

            $data = array_merge($data, [
                'investor_type' => $investorTypeValue,
                'preferred_sector_id' => $preferredSectorId,
                'category_id' => $categoryId,
                'preferred_sector' => $this->relationLoaded('preferredSector') && $this->preferredSector ? [
                    'id' => $this->preferredSector->id,
                    'name' => $this->preferredSector->name,
                ] : null,
                'category' => $this->relationLoaded('category') && $this->category ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ] : null,
                'capital' => $this->capital,
                'available_capital' => $this->available_capital,
                'previous_investments' => $this->previous_investments_count,
                'investment_experience' => $experienceValue,
                'investor_capital' => $this->available_capital,
                'investment_count' => $this->previous_investments_count,
                'experience_level' => $this->experience_level,
                'investor_experience' => $experienceValue,
                'statistics' => [
                    'purchased_seats_count' => (int) ($this->purchased_seats_count ?? 0),
                    'successful_investments_count' => (int) ($this->successful_investments_count ?? 0),
                    'capital' => $this->capital,
                ],
            ]);
        }

        if ($this->role === UserRole::Advertiser) {
            if (! isset($this->my_ads_count) || ! isset($this->successful_deals_count) || ! isset($this->awarded_investments_count)) {
                $this->resource->loadCount([
                    'opportunities as my_ads_count',
                    'opportunities as successful_deals_count' => fn ($query) => $query
                        ->where('status', OpportunityStatus::Completed->value),
                    'opportunities as awarded_investments_count' => fn ($query) => $query
                        ->whereNotNull('investor_id')
                        ->whereIn('status', [
                            OpportunityStatus::Reserved->value,
                            OpportunityStatus::Completed->value,
                        ]),
                ]);
            }

            $data = array_merge($data, [
                'company_license_url' => $this->company_license_url,
                'statistics' => [
                    'my_ads_count' => (int) ($this->my_ads_count ?? 0),
                    'successful_deals_count' => (int) ($this->successful_deals_count ?? 0),
                    'awarded_investments_count' => (int) ($this->awarded_investments_count ?? 0),
                ],
                'my_statistics' => [
                    'my_ads_count' => (int) ($this->my_ads_count ?? 0),
                    'successful_deals_count' => (int) ($this->successful_deals_count ?? 0),
                    'awarded_investments_count' => (int) ($this->awarded_investments_count ?? 0),
                ],
            ]);
        }

        return $data;
    }

    protected function formattedUserName(): string
    {
        $rolePrefix = $this->role === UserRole::Investor ? 'INV' : 'ADV';

        return sprintf('USR-%s-ID-%s', $rolePrefix, str_pad((string) $this->id, 3, '0', STR_PAD_LEFT));
    }
}
