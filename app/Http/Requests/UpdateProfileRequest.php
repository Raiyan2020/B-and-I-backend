<?php

namespace App\Http\Requests;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Models\Category;
use App\Models\PreferredSector;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('country_code') && $this->input('country_code') !== null) {
            $this->merge([
                'country_code' => (string) $this->input('country_code'),
            ]);
        }
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'display_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'short_description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'email' => [
                'sometimes',
                'email:dns,rfc',
                Rule::unique('users', 'email')
                    ->ignore($user?->id)
                    ->whereNull('deleted_at'),
            ],
            'phone' => [
                'sometimes',
                'regex:/^[4569]\d{7}$/',
                Rule::unique('users', 'phone')
                    ->ignore($user?->id)
                    ->whereNull('deleted_at'),
            ],
            'country_code' => ['sometimes', 'string', 'digits_between:1,5'],
            'company_license' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],

            'available_capital' => ['sometimes', 'nullable', 'numeric', 'min:1000'],
            'preferred_sector_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists(PreferredSector::class, 'id')->where('status', true),
            ],
            'category_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists(Category::class, 'id')->where('status', true),
            ],
            'investor_experience' => ['sometimes', 'nullable', new Enum(InvestorExperience::class)],
            'investor_type' => ['sometimes', 'nullable', new Enum(InvestorType::class)],
        ];
    }
}
