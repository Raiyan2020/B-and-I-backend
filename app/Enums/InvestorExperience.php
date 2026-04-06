<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum InvestorExperience: string
{
    use EnumRetriever;
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Expert = 'expert';
}
