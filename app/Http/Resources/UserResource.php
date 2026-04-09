<?php

namespace App\Http\Resources;

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
        $data = [
            'id' => $this->id,
            'role' => $this->role->value,
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
            'token' => $this->token ?? null,
        ];

        if ($this->role === UserRole::Investor) {
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
                'preferred_sector' => $this->preferredSector ? [
                    'id' => $this->preferredSector->id,
                    'name' => $this->preferredSector->name,
                ] : null,
                'category' => $this->category ? [
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
            ]);
        }

        if ($this->role === UserRole::Advertiser) {
            $data = array_merge($data, [
                'company_license_url' => $this->company_license_url,
            ]);
        }

        return $data;
    }
}
