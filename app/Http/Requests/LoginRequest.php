<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $countryCode = $this->input('country_code');
        $digitsRule = ($countryCode === '+965' || $countryCode === '965') ? 'digits:8' : 'digits_between:8,15';

        $phoneRules = ['nullable', $digitsRule, 'required_without:email'];
        if ($countryCode === '+965' || $countryCode === '965') {
            $phoneRules[] = 'regex:/^[4569]/';
        }

        return [
            'email' => ['nullable', 'email', 'required_without:phone'],
            'phone' => $phoneRules,
            'country_code' => ['nullable', 'string', 'digits_between:1,5', 'required_with:phone'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string',Rule::in([UserRole::Investor->value,UserRole::Advertiser->value])],
            'device_token' => ['nullable', 'string', 'max:2048'],
            'device_type' => ['nullable', Rule::in(DeviceType::values()), 'required_with:device_token'],
        ];
    }

    public function messages(): array
    {
        $countryCode = $this->input('country_code');
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

    public function prepareForValidation(): void
    {
        if ($this->filled('phone') && ! $this->filled('country_code')) {
            $this->merge([
                'country_code' => '965',
            ]);
        }
    }
}
