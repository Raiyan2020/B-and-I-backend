<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum InvestorType: string
{
    use EnumRetriever;
    case Angel = 'angel';
    case Company = 'company';
    case Crowdfunding = 'crowdfunding';

}
