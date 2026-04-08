<?php

namespace App\Http\Requests\Api\V1\Opportunities;

use App\Enums\OpportunityGoal;
use App\Enums\UserRole;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Advertiser;
    }

    public function rules(): array
    {
        $goal = $this->input('goal');

        return [
            'goal' => ['required', new Enum(OpportunityGoal::class)],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_email' => ['required', 'email'],
            'owner_name' => ['required', 'string', 'max:255'],
            'admin_company_name' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:100'],
            'company_name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists(Category::class, 'id')->where('status', true),
            ],
            'business_age_years' => ['required', 'integer', 'min:0', 'max:200'],
            'investment_required' => ['required', 'numeric', 'min:0'],
            'business_stage' => ['required', 'string', 'max:255'],
            'sale_percentage' => [
                Rule::requiredIf($goal === OpportunityGoal::RequestInvestment->value),
                'nullable',
                'numeric',
                'min:0.01',
                'max:100',
            ],
            'legal_entity' => ['required', 'string', 'max:255'],
            'financial_status' => ['required', 'string', 'max:255'],
            'investment_reason' => ['required', 'string', 'max:3000'],
            'full_description' => ['required', 'string', 'max:10000'],
            'terms_accepted' => ['accepted'],
        ];
    }
}
