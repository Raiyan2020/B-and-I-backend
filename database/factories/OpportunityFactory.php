<?php

namespace Database\Factories;

use App\Enums\OpportunityGoal;
use App\Enums\OpportunityStatus;
use App\Models\Category;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'goal' => OpportunityGoal::RequestInvestment,
            'status' => OpportunityStatus::Pending,
            'contact_name' => $this->faker->name(),
            'contact_phone' => '80808080',
            'contact_email' => $this->faker->safeEmail(),
            'owner_name' => $this->faker->name(),
            'admin_company_name' => $this->faker->company(),
            'license_number' => 'LIC-'.$this->faker->unique()->numberBetween(1000, 9999),
            'company_name' => $this->faker->company(),
            'business_age_years' => 3,
            'investment_required' => 65000,
            'business_stage' => 'تشغيل فعلي',
            'sale_percentage' => 20,
            'legal_entity' => 'شركة ذات مسؤولية محدودة',
            'financial_status' => 'مستقر',
            'investment_reason' => 'توسيع النشاط وفتح فروع جديدة.',
            'full_description' => 'وصف كامل للإعلان.',
        ];
    }
}
