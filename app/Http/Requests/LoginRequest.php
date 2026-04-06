<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')
                ->whereNull('deleted_at')->where('role', $this->role)],
            'phone' => ['required', 'regex:/^[4569]\d{7}$/', Rule::exists('users', 'phone')
                ->whereNull('deleted_at')
                ->where('role', $this->role)],
            'role' => ['required', new Enum(UserRole::class)],
            'country_code' => ['required', 'string', 'digits_between:1,5'],
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'country_code' => $this->country_code ?? '965',
        ]);
    }
}
