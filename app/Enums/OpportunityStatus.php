<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum OpportunityStatus: string
{
    use EnumRetriever;

    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case NeedsModification = 'needs_modification';
}
