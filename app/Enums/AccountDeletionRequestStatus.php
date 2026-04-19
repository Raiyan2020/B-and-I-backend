<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum AccountDeletionRequestStatus: string
{
    use EnumRetriever;

    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
