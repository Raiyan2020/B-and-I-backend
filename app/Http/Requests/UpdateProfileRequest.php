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
        $countryCode = $this->input('country_code', $user?->country_code);
        $isKuwait = ($countryCode === '+965' || $countryCode === '965');
        $digitsRule = $isKuwait ? 'digits:8' : 'digits_between:8,15';

        $phoneRules = [
            'required_with:country_code',
            'sometimes',
            $digitsRule,
            Rule::unique('users', 'phone')
                ->where('country_code', $countryCode)
                ->whereNull('deleted_at')
                ->ignore($user?->id),
        ];

        if ($isKuwait) {
            $phoneRules[] = 'regex:/^[4569]/';
        }

        $rules = [
            // Shared rules between Investor and Company
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => $phoneRules,
            'country_code' => [
                'required_with:phone',
                'sometimes',
                'string',
                'digits_between:1,5',
            ],
        ];
        if ($user->isCompany()) {
            // For company accounts
            $rules = array_merge($rules, [
                'company_license' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            ]);
        }

        if ($user->isInvestor()) {
            // For investor accounts
            $rules = array_merge($rules, [
                'investor_type' => ['sometimes', new Enum(InvestorType::class)],
                'capital' => ['sometimes', 'numeric', 'min:1000' ,'max:1000000000'],
                'available_capital' => ['sometimes', 'nullable', 'numeric', 'min:1000' ,'max:1000000000'],
                'preferred_sector_id' => [
                    'sometimes',
                    'integer',
                    Rule::exists(PreferredSector::class, 'id')->where('status', true),
                ],
                'category_id' => [
                    'sometimes',
                    'integer',
                    Rule::exists(Category::class, 'id')->where('status', true),
                ],
                'experience_level' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
                'previous_investments_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
                'investor_experience' => ['sometimes', 'nullable', new Enum(InvestorExperience::class)],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        $user = $this->user();
        $countryCode = $this->input('country_code', $user?->country_code);
        $phoneStart = null;

        if ($countryCode) {
            $country = \App\Helpers\CountryHelper::getCountryByCode($countryCode);
            if ($country && isset($country['phone_start'])) {
                $phoneStart = $country['phone_start'];
            }
        }

        $isKuwait = ($countryCode === '+965' || $countryCode === '965');
        if ($isKuwait) {
            $phoneStart = '5, 4, 6, 9';
        }

        $message = __('dashboard.phone_must_start_with');
        if ($phoneStart) {
            $message = str_replace(':start', $phoneStart, $message);
        } else {
            $message = str_replace(' :start', '', $message);
            $message = str_replace(':start', '', $message);
        }

        return [
            'phone.regex' => $message,
        ];
    }
}
