<?php

namespace App\Http\Requests;

use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:dns,rfc',Rule::exists('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'otp' => ['required', 'digits:6'],
            'device_token' => ['nullable', 'string', 'max:2048'],
            'device_type' => ['nullable', Rule::in(DeviceType::values()), 'required_with:device_token'],
        ];
    }
}
