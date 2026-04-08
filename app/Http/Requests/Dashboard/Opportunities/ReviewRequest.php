<?php

namespace App\Http\Requests\Dashboard\Opportunities;

use App\Enums\OpportunityStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(OpportunityStatus::class)],
            'review_note' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
