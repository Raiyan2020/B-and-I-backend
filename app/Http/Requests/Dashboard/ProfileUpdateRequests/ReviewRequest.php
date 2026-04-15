<?php

namespace App\Http\Requests\Dashboard\ProfileUpdateRequests;

use App\Enums\ProfileUpdateRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'status' => [
                'required',
                new Enum(ProfileUpdateRequestStatus::class),
                Rule::in([
                    ProfileUpdateRequestStatus::Approved->value,
                    ProfileUpdateRequestStatus::Rejected->value,
                ]),
            ],
            'rejection_reason' => [
                Rule::requiredIf(fn () => $this->input('status') === ProfileUpdateRequestStatus::Rejected->value),
                'nullable',
                'string',
                'max:3000',
            ],
        ];
    }
}
