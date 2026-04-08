<?php

namespace App\Http\Requests\Api\V1\General;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListPublicInvestorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'investor_type' => ['sometimes', 'nullable', Rule::enum(InvestorType::class)],
            'min_capital' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_capital' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'investor_experience' => ['sometimes', 'nullable', Rule::enum(InvestorExperience::class)],
            'preferred_sector_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('preferred_sectors', 'id')->whereNull('deleted_at'),
            ],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('min_capital') || ! $this->filled('max_capital')) {
                return;
            }
            if ((float) $this->max_capital < (float) $this->min_capital) {
                $validator->errors()->add(
                    'max_capital',
                    'The max capital must be greater than or equal to min capital.'
                );
            }
        });
    }
}
