<?php

namespace App\Http\Requests\Dashboard\SubscriptionPackages;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'array'],
            'description.ar' => ['required', 'string', 'max:65000'],
            'description.en' => ['required', 'string', 'max:65000'],
            'status' => ['required', 'boolean'],
        ];
    }
}
