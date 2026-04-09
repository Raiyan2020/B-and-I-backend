<?php

namespace App\Enums;

enum DeviceType: string
{
    case Web = 'web';
    case Android = 'android';
    case Ios = 'ios';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function normalize(?string $value): self
    {
        return self::tryFrom(strtolower((string) $value)) ?? self::Web;
    }
}
