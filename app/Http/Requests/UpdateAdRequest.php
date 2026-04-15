<?php

namespace App\Http\Requests;

use App\Enums\OpportunityGoal;
use App\Enums\UserRole;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Advertiser;
    }

    public function rules(): array
    {
        $goal = $this->input('goal');

        return [
            'goal' => ['sometimes', new Enum(OpportunityGoal::class)],
            'image' => ['nullable', 'image', 'max:2024', 'mimetypes:image/jpeg,image/png,image/jpg,image/webp'],
            'company_name' => ['sometimes', 'string', 'max:255'],
            'category_id' => [
                'sometimes',
                'integer',
                Rule::exists(Category::class, 'id')->where('status', true),
            ],
            'business_age_years' => ['sometimes', 'integer', 'min:0', 'max:200'],
            'investment_required' => ['sometimes', 'numeric', 'min:0'],
            'business_stage' => ['sometimes', 'string', 'max:255'],
            'sale_percentage' => [
                Rule::requiredIf($goal === OpportunityGoal::RequestInvestment->value),
                'nullable',
                'numeric',
                'min:0.01',
                'max:100',
            ],
            'legal_entity' => ['sometimes', 'string', 'max:255'],
            'financial_status' => ['sometimes', 'string', 'max:255'],
            'investment_reason' => ['sometimes', 'string', 'max:3000'],
            'full_description' => ['sometimes', 'string', 'max:10000'],
        ];
    }
}
