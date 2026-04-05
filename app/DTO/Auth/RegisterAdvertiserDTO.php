<?php

namespace App\DTO\Auth;

use Illuminate\Http\UploadedFile;

final class RegisterAdvertiserDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone,
        public string $password,
        public string $company_name,
        public string $license_number,
        public UploadedFile $company_license,
        public bool $agreed_to_terms
    ) {}

    public static function fromRequest(array $v, UploadedFile $file): self
    {
        return new self(
            $v['first_name'],
            $v['last_name'],
            $v['email'],
            $v['phone'],
            $v['password'],
            $v['company_name'],
            $v['license_number'],
            $file,
            (bool)($v['agreed_to_terms'] ?? false)
        );
    }
}
