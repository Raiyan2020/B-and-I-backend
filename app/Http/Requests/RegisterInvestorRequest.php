<?php

namespace App\Http\Requests;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Models\Category;
use App\Models\PreferredSector;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RegisterInvestorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Maps UI/API names to DB fields:
     * المجال المفضل: preferred_sector_id (أو preferred_sector) → preferred_sectors.id
     * قطاع التركيز: category_id → users.category_id
     * available_capital → investor_capital (request) → users.available_capital (و users.capital إن لم يُرسل capital)
     * previous_investments → investment_count (request) → users.previous_investments_count
     * investment_experience → investor_experience
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'country_code' => $this->country_code ?? '965',
        ]);
    }

    public function rules(): array
    {
        $countryCode = $this->input('country_code');
        $digitsRule = ($countryCode === '+965' || $countryCode === '965') ? 'digits:8' : 'digits_between:8,15';

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:dns,rfc', Rule::unique('users', 'email')
                ->whereNull('deleted_at')],
            'phone' => ['required', $digitsRule, Rule::unique('users', 'phone')->whereNull('deleted_at')],
            'country_code' => ['required', 'string', 'digits_between:1,5'],
            'password' => ['required', 'string', 'min:8', 'max:100'],

            'investor_type' => ['required', new Enum(InvestorType::class)],
            'capital' => ['required', 'numeric', 'min:1000'],
            'available_capital' => ['required', 'numeric', 'min:1000'],
            'preferred_sector_id' => [
                'required',
                'integer',
                Rule::exists(PreferredSector::class, 'id')->where('status', true),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists(Category::class, 'id')->where('status', true),
            ],
            'experience_level' => ['required', 'numeric', 'min:0', 'max:100'],
            'previous_investments_count' => ['required', 'integer', 'min:0'],
            'investor_experience' => ['required', new Enum(InvestorExperience::class)],
            'agreed_to_terms' => ['accepted'],
        ];
    }
}
