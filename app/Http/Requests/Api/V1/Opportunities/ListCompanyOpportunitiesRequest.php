<?php

namespace App\Http\Requests\Api\V1\Opportunities;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class ListCompanyOpportunitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Advertiser;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'status' => ['nullable', 'string', 'in:pending,needs_revision,published,reserved,completed'],
            'goal' => ['nullable', 'string', 'in:sell_business,request_investment'],
        ];
    }
}
