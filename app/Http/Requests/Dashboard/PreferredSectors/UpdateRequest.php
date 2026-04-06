<?php

namespace App\Http\Requests\Dashboard\PreferredSectors;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ];
    }
}
