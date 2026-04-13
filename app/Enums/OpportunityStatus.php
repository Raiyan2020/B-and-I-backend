<?php

namespace App\Enums;

use App\Traits\EnumRetriever;

enum OpportunityStatus: string
{
    use EnumRetriever;

    case Pending = 'pending';
    case NeedsRevision = 'needs_revision';
    case Published = 'published';
    case Reserved = 'reserved';
    case Completed = 'completed';
}
