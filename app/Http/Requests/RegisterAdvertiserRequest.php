<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAdvertiserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:dns,rfc', 'unique:users,email'],
            'phone' => ['required', 'regex:/^[4569]\d{7}$/'],
            'country_code' => ['required', 'string', 'digits_between:1,5'],
            'password' => ['required', 'string', 'min:8','max:100'],
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
