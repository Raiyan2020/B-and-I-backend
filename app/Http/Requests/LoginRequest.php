<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'regex:/^[4569]\d{7}$/', 'required_without:email'],
            'country_code' => ['nullable', 'string', 'digits_between:1,5', 'required_with:phone'],
            'password' => ['required', 'string'],
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
