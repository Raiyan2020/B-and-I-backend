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
            'email' => ['required','email'],
            'phone' => ['required','regex:/^\d{8}$/'],
            'role' => ['required', 'in:investor,advertiser'],
        ];
    }
}
