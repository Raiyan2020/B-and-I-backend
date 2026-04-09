<?php

namespace App\Http\Requests;

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
        return [
            'email' => ['nullable', 'email', 'required_without:phone'],
            'phone' => ['nullable', 'regex:/^[4569]\d{7}$/', 'required_without:email'],
            'country_code' => ['nullable', 'string', 'digits_between:1,5', 'required_with:phone'],
            'password' => ['required', 'string'],
            'device_token' => ['nullable', 'string', 'max:2048'],
            'device_type' => ['nullable', Rule::in(DeviceType::values()), 'required_with:device_token'],
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
