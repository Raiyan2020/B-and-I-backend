<?php

namespace App\Services\Opportunity;

use App\Enums\OpportunityStatus;
use App\Enums\OpportunityGoal;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Opportunity;
use App\Models\User;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

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
            'status' => OpportunityStatus::Pending,
        ]));
    }

    public function updateForCompany(User $user, Opportunity $opportunity, array $data): Opportunity
    {
        $this->assertOwnership($user, $opportunity);

        if (($opportunity->status?->value ?? $opportunity->status) !== OpportunityStatus::NeedsRevision->value) {
            throw ValidationException::withMessages([
                'status' => [__('apis.ad_edit_requires_needs_revision')],
            ]);
        }

        unset($data['terms_accepted']);
        $data = $this->normalizeGoalSpecificFields($data);

        $opportunity->update(array_merge($data, [
            'status'               => OpportunityStatus::Pending,
            'review_note'          => null,
            'reviewed_by_admin_id' => null,
            'reviewed_at'          => null,
        ]));

        return $opportunity->refresh(['category', 'reviewer', 'user']);
    }

    public function listForCompany(User $user, array $filters = []): LengthAwarePaginator
    {
        $perPage = (int)($filters['per_page'] ?? 15);

        return $user->opportunities()
            ->with(['category', 'reviewer'])
            ->when(!empty($filters['status']), fn($query) => $query->where('status', $filters['status']))
            ->when(!empty($filters['goal']), fn($query) => $query->where('goal', $filters['goal']))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
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
            ->whereIn('status', [
                OpportunityStatus::Published,
                OpportunityStatus::Reserved,
            ])
            ->latest()
            ->when($options->paginateNum > 0, fn($query) => $query->limit($options->paginateNum))
            ->get();
    }

    public function reviewByAdmin(Admin $admin, Opportunity $opportunity, OpportunityStatus $status, ?string $reviewNote): Opportunity
    {
        if (!in_array($status, [OpportunityStatus::Published, OpportunityStatus::NeedsRevision], true)) {
            throw new \InvalidArgumentException(__('apis.invalid_opportunity_status_transition'));
        }

        $opportunity->update([
            'status'               => $status,
            'review_note'          => $reviewNote,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at'          => now(),
        ]);

        return $opportunity->refresh(['category', 'reviewer', 'user']);
    }

    public function dashboardIndex(QueryOptions $options): Collection
    {
        return Opportunity::query()
            ->with(['category', 'user', 'reviewer'])
            ->when($options->conditions, fn($query) => $query->where($options->conditions))
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
