<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * بطاقة مستثمر للعرض العام (دليل المستثمرين).
 *
 * @mixin \App\Models\User
 */
class PublicInvestorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = $this->investor_type;
        $typeValue = $type instanceof \BackedEnum ? $type->value : $type;

        $experience = $this->investor_experience;
        $experienceValue = $experience instanceof \BackedEnum ? $experience->value : $experience;

        return [
            'id' => $this->id,
            'display_id' => 'USR-'.$this->id,
            'investor_type' => [
                'value' => $typeValue,
                'label' => $typeValue ? __('enums.investor_type.'.$typeValue) : null,
            ],
            'available_capital' => $this->available_capital !== null
                ? (float) $this->available_capital
                : null,
            'focus_sector' => $this->preferredSector
                ? [
                    'id' => $this->preferredSector->id,
                    'name' => $this->preferredSector->getTranslation('name', app()->getLocale()),
                ]
                : null,
            'investment_experience' => [
                'value' => $experienceValue,
                'label' => $experienceValue ? __('enums.investor_experience.'.$experienceValue) : null,
            ],
            'image' => $this->image,
        ];
    }
}
