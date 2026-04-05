<?php

namespace App\DTO\Auth;

final class LoginDTO
{
    public function __construct(
        public string $email,
        public string $phone,
        public string $role
    ) {}

    public static function fromRequest(array $v): self
    {
        return new self(
            email: $v['email'],
            phone: $v['phone'],
            role: $v['role']
        );
    }
}
