<?php

namespace App\DTO\Auth;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\UserRole;

final class RegisterInvestorDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone,
        public string $country_code,
        public string $password,
        public InvestorType $investor_type,
        /** المجال المفضل — معرّف من جدول preferred_sectors */
        public int $preferred_sector_id,
        /** قطاع التركيز — معرّف القسم من جدول categories (users.category_id) */
        public int $category_id,
        /** رأس المال المتاح — يُخزَّن في users.available_capital */
        public float $available_capital,
        /** رأس المال — يُخزَّن في users.capital */
        public float $capital,
        /** خبرة الاستثمار — يُخزَّن في investor_experience */
        public InvestorExperience $investment_experience,
        public UserRole $role,
        public float $experience_level,
        public int $previous_investments_count,
        public bool $agreed_to_terms
    ) {}

    public static function fromRequest(array $v): self
    {
        return new self(
            first_name: $v['first_name'],
            last_name: $v['last_name'],
            email: $v['email'],
            phone: $v['phone'],
            country_code: $v['country_code'],
            password: $v['password'],
            investor_type: InvestorType::from($v['investor_type']),
            preferred_sector_id: (int) $v['preferred_sector_id'],
            category_id: (int) $v['category_id'],
            available_capital: (float) $v['available_capital'],
            capital: (float) ($v['capital'] ?? $v['capital']),
            investment_experience: InvestorExperience::from($v['investor_experience']),
            experience_level: (float) $v['experience_level'],
            previous_investments_count: (int) $v['previous_investments_count'],
            role: UserRole::Investor,
            agreed_to_terms: (bool) ($v['agreed_to_terms'] ?? false),
        );
    }
}
