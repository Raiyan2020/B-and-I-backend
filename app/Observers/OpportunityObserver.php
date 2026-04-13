<?php

namespace App\Observers;

use App\Enums\OpportunityStatus;
use App\Models\Opportunity;

class OpportunityObserver
{
    public function created(Opportunity $opportunity): void
    {
        if (! empty($opportunity->opportunity_number)) {
            return;
        }

        $year = $opportunity->created_at?->format('Y') ?? now()->format('Y');
        $sequence = str_pad((string) $opportunity->id, 3, '0', STR_PAD_LEFT);

        $opportunity->forceFill([
            'opportunity_number' => "PROJ-{$year}-{$sequence}",
        ])->saveQuietly();
    }

    public function updating(Opportunity $opportunity): void
    {
        if (! $opportunity->isDirty('status')) {
            return;
        }

        $nextStatus = $opportunity->status instanceof OpportunityStatus
            ? $opportunity->status
            : OpportunityStatus::tryFrom((string) $opportunity->status);

        if ($nextStatus === OpportunityStatus::Published && ! empty($opportunity->investor_id)) {
            $opportunity->investor_id = null;
        }
    }
}
