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
            'first_name' => ['required','string'],
            'last_name' => ['required','string'],
            'email' => ['required','email','unique:users,email'],
            'phone' => ['required','regex:/^\d{8}$/'],
            'password' => ['required','string','min:8','confirmed'],
            'company_name' => ['required','string'],
            'license_number' => ['required','string'],
            'company_license' => ['required','file','mimes:jpg,jpeg,png,pdf','max:10240'],
            'agreed_to_terms' => ['accepted'],
        ];
    }
}
