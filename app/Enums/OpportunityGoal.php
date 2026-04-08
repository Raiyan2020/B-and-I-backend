<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum OpportunityGoal: string
{
    use EnumRetriever;

    case SellBusiness = 'sell_business';
    case RequestInvestment = 'request_investment';
}
