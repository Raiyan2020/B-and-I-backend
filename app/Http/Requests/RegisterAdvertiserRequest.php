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

        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:dns,rfc', Rule::unique('users', 'email')],
            'phone' => [
                'required',
                $digitsRule,
                Rule::unique('users', 'phone')->whereNull('deleted_at')
            ],
            'country_code' => ['required', 'string', 'digits_between:1,5'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'company_license' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'agreed_to_terms' => ['accepted'],
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'country_code' => $this->country_code ?? '965',
        ]);
    }
}
