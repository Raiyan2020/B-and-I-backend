<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'orders' => ['sometimes', 'boolean'],
            'interest' => ['sometimes', 'boolean'],
            'system' => ['sometimes', 'boolean'],
        ];
    }
}
