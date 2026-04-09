<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum NotificationCategory: string
{
    use EnumRetriever;
    case Interest = 'interest';
    case Orders = 'orders';
    case System = 'system';

    public static function normalize(string|self|null $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom((string) $value) ?? self::System;
    }
}
