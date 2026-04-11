<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestNewEmailChangeOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:dns,rfc',
                Rule::unique('users', 'email')
                    ->ignore($this->user()?->id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
