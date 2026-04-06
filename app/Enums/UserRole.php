<?php

namespace App\Enums;

enum UserRole: string
{
    case Investor = 'investor';
    case Advertiser = 'advertiser';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
