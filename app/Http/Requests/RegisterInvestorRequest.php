<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterInvestorRequest extends FormRequest
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
            'investor_type' => ['required','in:angel,company,crowdfunding'],
            'investor_sector' => ['required','string'],
            'investor_capital' => ['required','numeric','min:1000'],
            'investment_count' => ['required','integer','min:0'],
            'investor_experience' => ['required','in:beginner,intermediate,expert'],
            'agreed_to_terms' => ['accepted'],
        ];
    }
}
