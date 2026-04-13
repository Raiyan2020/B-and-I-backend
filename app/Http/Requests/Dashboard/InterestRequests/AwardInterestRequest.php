<?php

namespace App\Http\Requests\Dashboard\InterestRequests;

use App\Enums\OpportunityStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AwardInterestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    OpportunityStatus::Reserved->value,
                    OpportunityStatus::Completed->value,
                ]),
            ],
        ];
    }
}
