<?php

namespace App\Observers;

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
}
