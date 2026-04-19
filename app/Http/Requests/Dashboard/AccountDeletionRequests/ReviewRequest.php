<?php

namespace App\Http\Requests\Dashboard\AccountDeletionRequests;

use App\Enums\AccountDeletionRequestStatus;
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
                new Enum(AccountDeletionRequestStatus::class),
                Rule::in([
                    AccountDeletionRequestStatus::Approved->value,
                    AccountDeletionRequestStatus::Rejected->value,
                ]),
            ],
            'rejection_reason' => [
                Rule::requiredIf(fn () => $this->input('status') === AccountDeletionRequestStatus::Rejected->value),
                'nullable',
                'string',
                'max:3000',
            ],
        ];
    }
}
