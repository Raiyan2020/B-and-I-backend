<?php

namespace App\DTO\Auth;

use App\Enums\UserRole;

final class LoginDTO
{
    public function __construct(
        public string $email,
        public string $phone,
        public UserRole $role
    ) {}

    public static function fromRequest(array $v): self
    {
        return new self(
            email: $v['email'],
            phone: $v['phone'],
            role: UserRole::from($v['role'])
        );
    }
}
