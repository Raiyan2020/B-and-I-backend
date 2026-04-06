<?php

namespace App\DTO\Auth;

use App\Enums\UserRole;
use Illuminate\Http\UploadedFile;

final class RegisterAdvertiserDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone,
        public string $country_code,
        public string $password,
        public UserRole $role,
        public UploadedFile $company_license,
        public bool $agreed_to_terms
    ) {}

    public static function fromRequest(array $v, UploadedFile $file): self
    {
        return new self(
            first_name: $v['first_name'],
            last_name: $v['last_name'],
            email: $v['email'],
            phone: $v['phone'],
            country_code: $v['country_code'],
            password: $v['password'],
            company_license: $file,
            role: UserRole::Advertiser,
            agreed_to_terms: (bool)($v['agreed_to_terms'] ?? false)
        );
    }
}
