<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case Orders = 'orders';
    case Interest = 'interest';
    case System = 'system';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function normalize(string|self|null $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom((string) $value) ?? self::System;
    }
}
