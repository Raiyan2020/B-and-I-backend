<?php

namespace App\Services\Opportunity;

use App\Enums\OpportunityStatus;
use App\Enums\OpportunityGoal;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Opportunity;
use App\Models\User;
use App\Support\QueryOptions;
use Illuminate\Support\Collection;

class OpportunityService
{
    public function createForCompany(User $user, array $data): Opportunity
    {
        if ($user->role !== UserRole::Advertiser) {
            throw new \InvalidArgumentException(__('apis.opportunity_company_only'));
        }

        unset($data['terms_accepted']);
        $data = $this->normalizeGoalSpecificFields($data);

        return $user->opportunities()->create(array_merge($data, [
            'status' => OpportunityStatus::PendingReview,
        ]));
    }

    public function updateForCompany(User $user, Opportunity $opportunity, array $data): Opportunity
    {
        $this->assertOwnership($user, $opportunity);

        unset($data['terms_accepted']);
        $data = $this->normalizeGoalSpecificFields($data);

        $opportunity->update(array_merge($data, [
            'status' => OpportunityStatus::PendingReview,
            'review_note' => null,
            'reviewed_by_admin_id' => null,
            'reviewed_at' => null,
        ]));

        return $opportunity->refresh(['category', 'reviewer', 'user']);
    }

    public function listForCompany(User $user): Collection
    {
        return $user->opportunities()
            ->with(['category', 'reviewer'])
            ->latest()
            ->get();
    }

    public function showForCompany(User $user, Opportunity $opportunity): Opportunity
    {
        $this->assertOwnership($user, $opportunity);

        return $opportunity->load(['category', 'reviewer', 'user']);
    }

    public function listApproved(QueryOptions $options): Collection
    {
        return Opportunity::query()
            ->with(['category', 'user'])
            ->where('status', OpportunityStatus::Approved)
            ->latest()
            ->when($options->paginateNum > 0, fn ($query) => $query->limit($options->paginateNum))
            ->get();
    }

    public function reviewByAdmin(Admin $admin, Opportunity $opportunity, OpportunityStatus $status, ?string $reviewNote): Opportunity
    {
        if (! in_array($status, [OpportunityStatus::Approved, OpportunityStatus::NeedsModification], true)) {
            throw new \InvalidArgumentException(__('apis.invalid_opportunity_status_transition'));
        }

        $opportunity->update([
            'status' => $status,
            'review_note' => $reviewNote,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $opportunity->refresh(['category', 'reviewer', 'user']);
    }

    public function dashboardIndex(QueryOptions $options): Collection
    {
        return Opportunity::query()
            ->with(['category', 'user', 'reviewer'])
            ->when($options->conditions, fn ($query) => $query->where($options->conditions))
            ->latest()
            ->search(request()->filters ?? [])
            ->get();
    }

    public function findForDashboard(int $id): Opportunity
    {
        return Opportunity::query()
            ->with(['category', 'user', 'reviewer'])
            ->findOrFail($id);
    }

    protected function assertOwnership(User $user, Opportunity $opportunity): void
    {
        if ($opportunity->user_id !== $user->id) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(__('apis.have_no_permission'));
        }
    }

    protected function normalizeGoalSpecificFields(array $data): array
    {
        $goal = $data['goal'] ?? null;

        if ($goal instanceof OpportunityGoal) {
            $goal = $goal->value;
        }

        if ($goal === OpportunityGoal::SellBusiness->value) {
            $data['sale_percentage'] = null;
        }

        return $data;
    }
}
