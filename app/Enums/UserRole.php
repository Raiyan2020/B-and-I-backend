<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum UserRole: string
{
    use EnumRetriever;
    case Investor = 'investor';
    case Advertiser = 'advertiser';

}
