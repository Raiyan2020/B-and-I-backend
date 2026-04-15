<?php

namespace App\Services\Opportunity;

use App\Enums\OpportunityStatus;
use App\Enums\OpportunityGoal;
use App\Enums\NotificationCategory;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpportunityService
{
    public function __construct(
        private readonly ?\App\Services\Notifications\GeneralNotificationService $notificationService = null,
    ) {}

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

        if (!in_array(($opportunity->status?->value ?? $opportunity->status), [OpportunityStatus::NeedsRevision->value, OpportunityStatus::Pending->value], true)) {
            throw ValidationException::withMessages([
                'status' => [__('apis.ad_edit_requires_needs_revision_or_pending_status')],
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
            ->withCount(['investmentSeats', 'interestRequests'])
            ->when(!empty($filters['status']), fn($query) => $query->where('status', $filters['status']))
            ->when(!empty($filters['goal']), fn($query) => $query->where('goal', $filters['goal']))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listWithPurchasedSeatsForCompany(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->opportunities()
            ->with(['category', 'reviewer'])
            ->withCount(['investmentSeats', 'interestRequests'])
            ->whereHas('investmentSeats',function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('interestRequests', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function showForCompany(User $user, Opportunity $opportunity): Opportunity
    {
        $this->assertOwnership($user, $opportunity);

        return $opportunity
            ->load(['category', 'reviewer', 'user'])
            ->loadCount(['investmentSeats', 'interestRequests']);
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
            ->withCount(['investmentSeats', 'interestRequests'])
            ->when($options->conditions, fn($query) => $query->where($options->conditions))
            ->latest()
            ->search(request()->filters ?? [])
            ->get();
    }

    public function findForDashboard(int $id): Opportunity
    {
        return Opportunity::query()
            ->with([
                'category',
                'user',
                'reviewer',
                'investor',
                'investmentSeats' => fn ($query) => $query->with('user')->latest('id')->limit(10),
                'interestRequests' => fn ($query) => $query->with(['user', 'investmentSeat'])->latest('id')->limit(10),
            ])
            ->findOrFail($id);
    }

    public function awardInterestRequest(
        Admin $admin,
        InterestRequest $interestRequest,
        OpportunityStatus $status,
    ): Opportunity {
        if (! in_array($status, [OpportunityStatus::Reserved, OpportunityStatus::Completed, OpportunityStatus::Published], true)) {
            throw ValidationException::withMessages([
                'status' => [__('dashboard.invalid_award_status')],
            ]);
        }

        $interestRequest->loadMissing(['opportunity', 'investmentSeat', 'user']);

        if (
            ! $interestRequest->investmentSeat
            || $interestRequest->investmentSeat->opportunity_id !== $interestRequest->opportunity_id
            || $interestRequest->investmentSeat->user_id !== $interestRequest->user_id
        ) {
            throw ValidationException::withMessages([
                'interest_request' => [__('dashboard.invalid_interest_request_award')],
            ]);
        }

        return DB::transaction(function () use ($admin, $interestRequest, $status) {
            $opportunity = $interestRequest->opportunity;

            $opportunity->update([
                'status' => $status,
                'investor_id' => $interestRequest->user_id,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

            return $opportunity->fresh(['category', 'user', 'reviewer', 'investor']);
        });
    }

    public function purchaseSeat(User $user, Opportunity $opportunity): InvestmentSeat
    {
        $this->assertOpportunityAvailableForSeatPurchase($opportunity);

        if ($opportunity->investmentSeats()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'seat' => [__('apis.seat_already_purchased')],
            ]);
        }

        $seatPrice = GeneralSetting::getValueForKey('seat_price');

        if ($seatPrice === null || ! is_numeric($seatPrice)) {
            throw ValidationException::withMessages([
                'seat_price' => [__('apis.seat_price_not_configured')],
            ]);
        }

        // TODO:: Handle payment processing here before creating the seat

        $seat = DB::transaction(function () use ($user, $opportunity, $seatPrice) {
            return InvestmentSeat::query()->create([
                'user_id' => $user->id,
                'opportunity_id' => $opportunity->id,
                'price_paid' => (float) $seatPrice,
                'purchased_at' => now(),
            ]);
        });

        $this->notifyAdminsAboutSeatPurchase($user, $opportunity, $seat);

        return $seat->refresh();
    }

    public function submitInterest(User $user, Opportunity $opportunity): InterestRequest
    {
        $this->assertOpportunityAvailableForInterest($opportunity);

        $seat = $opportunity->investmentSeats()
            ->where('user_id', $user->id)
            ->first();

        if (! $seat) {
            throw ValidationException::withMessages([
                'seat' => [__('apis.interest_requires_seat_purchase')],
            ]);
        }

        if ($opportunity->interestRequests()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'interest' => [__('apis.interest_already_submitted')],
            ]);
        }

        $interestRequest = DB::transaction(function () use ($user, $opportunity, $seat) {
            return InterestRequest::query()->create([
                'user_id' => $user->id,
                'opportunity_id' => $opportunity->id,
                'investment_seat_id' => $seat->id,
            ]);
        });

        $this->notifyAdminsAboutInterest($user, $opportunity, $interestRequest);

        return $interestRequest->refresh();
    }

    protected function assertOwnership(User $user, Opportunity $opportunity): void
    {
        if ($opportunity->user_id !== $user->id) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(__('apis.have_no_permission'));
        }
    }


    protected function assertOpportunityAvailableForSeatPurchase(Opportunity $opportunity): void
    {
        $status = $opportunity->status?->value ?? $opportunity->status;

        if (! in_array($status, [
            OpportunityStatus::Published->value,
            OpportunityStatus::Reserved->value,
        ], true)) {
            throw ValidationException::withMessages([
                'opportunity' => [__('apis.seat_purchase_unavailable_for_opportunity')],
            ]);
        }

        if ($status === OpportunityStatus::Reserved->value) {
            throw ValidationException::withMessages([
                'opportunity' => [__('apis.reserved_ads_block_new_seat_purchases')],
            ]);
        }
    }

    protected function assertOpportunityAvailableForInterest(Opportunity $opportunity): void
    {
        $status = $opportunity->status?->value ?? $opportunity->status;

        if (! in_array($status, [
            OpportunityStatus::Published->value,
            OpportunityStatus::Reserved->value,
        ], true)) {
            throw ValidationException::withMessages([
                'opportunity' => [__('apis.interest_submission_unavailable_for_opportunity')],
            ]);
        }
    }

    protected function notifyAdminsAboutSeatPurchase(User $user, Opportunity $opportunity, InvestmentSeat $seat): void
    {
        if (! $this->notificationService) {
            return;
        }

        $admins = collect();

        $this->notificationService->sendToUsers($admins, new GeneralNotification(
            title: [
                'ar' => 'تم شراء كراسة استثمار',
                'en' => 'Investment seat purchased',
            ],
            body: [
                'ar' => "قام {$user->name} بشراء كراسة للإعلان {$opportunity->company_name}",
                'en' => "{$user->name} purchased a seat for {$opportunity->company_name}",
            ],
            notificationType: 'investment_seat_purchased',
            category: NotificationCategory::Interest,
            model: $seat,
            payload: [
                'opportunity_id' => $opportunity->id,
                'opportunity_name' => $opportunity->company_name,
                'investor_id' => $user->id,
            ],
        ));
    }

    protected function notifyAdminsAboutInterest(User $user, Opportunity $opportunity, InterestRequest $interestRequest): void
    {
        if (! $this->notificationService) {
            return;
        }

        $admins = collect();

        $this->notificationService->sendToUsers($admins, new GeneralNotification(
            title: [
                'ar' => 'تم تسجيل اهتمام جديد',
                'en' => 'New interest registered',
            ],
            body: [
                'ar' => "قام {$user->name} بتسجيل اهتمامه بالإعلان {$opportunity->company_name}",
                'en' => "{$user->name} registered interest in {$opportunity->company_name}",
            ],
            notificationType: 'interest_request_created',
            category: NotificationCategory::Interest,
            model: $interestRequest,
            payload: [
                'opportunity_id' => $opportunity->id,
                'opportunity_name' => $opportunity->company_name,
                'investor_id' => $user->id,
            ],
        ));
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
