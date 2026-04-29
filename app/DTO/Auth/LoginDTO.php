<?php

namespace App\DTO\Auth;

final class LoginDTO
{
    public function __construct(
        public ?string $email,
        public ?string $phone,
        public ?string $country_code,
        public string $password,
        public ?string $role,
        public ?string $device_token,
        public ?string $device_type,
    ) {}

    public static function fromRequest(array $v): self
    {
        return new self(
            email: $v['email'] ?? null,
            phone: $v['phone'] ?? null,
            country_code: $v['country_code'] ?? null,
            password: $v['password'],
            role: $v['role'],
            device_token: $v['device_token'] ?? null,
            device_type: $v['device_type'] ?? null,
        );
    }
}
