<?php

namespace App\Http\Requests\Dashboard\Users;

use App\Enums\UserRole;
use App\Helpers\CountryHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $countryCode = $this->input('country_code');
        $role = $this->input('role');
        $phoneStart = null;

        if ($countryCode) {
            $country = CountryHelper::getCountryByCode($countryCode);
            $phoneStart = $country['phone_start'] ?? null;
        }

        $phoneRules = ['required', 'digits_between:8,15', Rule::unique('users', 'phone')->whereNull('deleted_at')];

        if ($phoneStart && $this->input('phone')) {
            $phoneRules[] = 'regex:/^' . preg_quote($phoneStart, '/') . '/';
        }

        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::in(UserRole::values())],
            'email' => ['required', 'email:dns,rfc,spoof', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'phone' => $phoneRules,
            'country_code' => ['required', 'string', 'max:5'],
            'lang' => ['required', Rule::in(['ar', 'en'])],
            'company_license' => [Rule::requiredIf(fn () => $this->input('role') === UserRole::Advertiser->value), 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'investor_type' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), Rule::in(\App\Enums\InvestorType::values())],
            'capital' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), 'nullable', 'numeric', 'min:1000'],
            'available_capital' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), 'nullable', 'numeric', 'min:1000'],
            'preferred_sector_id' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), 'nullable', 'integer', Rule::exists('preferred_sectors', 'id')->where('status', true)],
            'category_id' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), 'nullable', 'integer', Rule::exists('categories', 'id')->where('status', true)],
            'experience_level' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), 'nullable', 'numeric', 'min:0', 'max:100'],
            'previous_investments_count' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), 'nullable', 'integer', 'min:0'],
            'investor_experience' => [Rule::requiredIf(fn () => $role === UserRole::Investor->value), Rule::in(\App\Enums\InvestorExperience::values())],
            'password' => ['required', 'confirmed', 'min:6', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        $countryCode = $this->input('country_code');
        $phoneStart = null;

        if ($countryCode) {
            $country = CountryHelper::getCountryByCode($countryCode);
            if ($country && isset($country['phone_start'])) {
                $phoneStart = $country['phone_start'];
            }
        }

        // Build the error message with the phone start digit
        $message = __('dashboard.phone_must_start_with');
        if ($phoneStart) {
            $message = str_replace(':start', $phoneStart, $message);
        } else {
            // If no phone start found, remove the placeholder
            $message = str_replace(' :start', '', $message);
            $message = str_replace(':start', '', $message);
        }

        return [
            'phone.regex' => $message,
        ];
    }
}
