<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterAdvertiserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $countryCode = $this->input('country_code');
        $digitsRule = ($countryCode === '+965' || $countryCode === '965') ? 'digits:8' : 'digits_between:8,15';

        $phoneRules = [
            'required',
            $digitsRule,
            Rule::unique('users', 'phone')->whereNull('deleted_at')
        ];
        if ($countryCode === '+965' || $countryCode === '965') {
            $phoneRules[] = 'regex:/^[4569]/';
        }

        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:dns,rfc', Rule::unique('users', 'email')],
            'phone' => $phoneRules,
            'country_code' => ['required', 'string', 'digits_between:1,5'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'company_license' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'agreed_to_terms' => ['accepted'],
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
    public function prepareForValidation()
    {
        $this->merge([
            'country_code' => $this->country_code ?? '965',
        ]);
    }
}
