<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum DeviceType: string
{
    use EnumRetriever;
    case Web = 'web';
    case Android = 'android';
    case Ios = 'ios';

    public static function normalize(?string $value): self
    {
        return self::tryFrom(strtolower((string) $value)) ?? self::Web;
    }
}
