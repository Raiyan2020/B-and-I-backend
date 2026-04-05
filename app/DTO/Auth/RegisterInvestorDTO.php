<?php

namespace App\DTO\Auth;

final class RegisterInvestorDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone,
        public string $password,
        public string $investor_type,
        public string $investor_sector,
        public float $investor_capital,
        public int $investment_count,
        public string $investor_experience,
        public bool $agreed_to_terms
    ) {}

    public static function fromRequest(array $v): self
    {
        return new self(
            $v['first_name'],
            $v['last_name'],
            $v['email'],
            $v['phone'],
            $v['password'],
            $v['investor_type'],
            $v['investor_sector'],
            isset($v['investor_capital']) ? (float)$v['investor_capital'] : 0.0,
            isset($v['investment_count']) ? (int)$v['investment_count'] : 0,
            $v['investor_experience'],
            (bool)($v['agreed_to_terms'] ?? false)
        );
    }
}
