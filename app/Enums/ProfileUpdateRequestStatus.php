<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum ProfileUpdateRequestStatus: string
{
    use EnumRetriever;

    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
