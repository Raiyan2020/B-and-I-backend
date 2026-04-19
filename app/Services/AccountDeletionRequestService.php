<?php

namespace App\Services;

use App\Enums\AccountDeletionRequestStatus;
use App\Enums\OpportunityStatus;
use App\Models\AccountDeletionRequest;
use App\Models\Admin;
use App\Models\FcmToken;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\Devices\DeviceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountDeletionRequestService
{
    public function __construct(
        private readonly NotificationCycleService $notificationCycleService,
        private readonly DeviceService $deviceService,
    ) {}

    public function latestForUser(User $user): ?AccountDeletionRequest
    {
        return $user->accountDeletionRequests()
            ->with('user')
            ->latest('id')
            ->first();
    }

    /**
     * @return array{status: string, request?: AccountDeletionRequest}
     */
    public function submit(User $user): array
    {
        $existingPending = $user->pendingAccountDeletionRequests()->latest('id')->first();

        if ($existingPending) {
            return [
                'status' => 'pending_request_exists',
                'request' => $existingPending,
            ];
        }

        $request = DB::transaction(function () use ($user) {
            return $user->accountDeletionRequests()->create([
                'status' => AccountDeletionRequestStatus::Pending,
            ]);
        });

        DB::afterCommit(fn () => $this->notificationCycleService->adminAccountDeletionSubmitted(
            $request->fresh(['user'])
        ));

        return [
            'status' => 'submitted',
            'request' => $request->fresh(['user']),
        ];
    }

    public function approve(Admin $admin, AccountDeletionRequest $accountDeletionRequest): AccountDeletionRequest
    {
        return DB::transaction(function () use ($admin, $accountDeletionRequest) {
            $accountDeletionRequest->refresh();

            if ($accountDeletionRequest->status !== AccountDeletionRequestStatus::Pending) {
                throw ValidationException::withMessages([
                    'status' => [__('dashboard.account_deletion_request_already_reviewed')],
                ]);
            }

            $user = $accountDeletionRequest->user()->lockForUpdate()->firstOrFail();

            $accountDeletionRequest->update([
                'status' => AccountDeletionRequestStatus::Approved,
                'rejection_reason' => null,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $this->notificationCycleService->userAccountDeletionApproved(
                $accountDeletionRequest->fresh(['user', 'reviewer'])
            );

            $user->tokens()->delete();
            $this->deviceService->forgetAllUserDevices($user);

            FcmToken::query()
                ->where('user_id', $user->id)
                ->delete();

            $user->authUpdates()->delete();
            $user->delete();

            return $accountDeletionRequest->fresh(['user' => fn ($query) => $query->withTrashed(), 'reviewer']);
        });
    }

    public function reject(Admin $admin, AccountDeletionRequest $accountDeletionRequest, string $reason): AccountDeletionRequest
    {
        return DB::transaction(function () use ($admin, $accountDeletionRequest, $reason) {
            $accountDeletionRequest->refresh();

            if ($accountDeletionRequest->status !== AccountDeletionRequestStatus::Pending) {
                throw ValidationException::withMessages([
                    'status' => [__('dashboard.account_deletion_request_already_reviewed')],
                ]);
            }

            $accountDeletionRequest->user()->lockForUpdate()->firstOrFail();

            $accountDeletionRequest->update([
                'status' => AccountDeletionRequestStatus::Rejected,
                'rejection_reason' => $reason,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $reviewedRequest = $accountDeletionRequest->fresh(['user', 'reviewer']);
            $this->notificationCycleService->userAccountDeletionRejected($reviewedRequest, $reason);

            return $reviewedRequest;
        });
    }

    /**
     * @return Collection<int, Opportunity>
     */
    public function activeAdvertisementsFor(User $user): Collection
    {
        if (! $user->isCompany()) {
            return new Collection();
        }

        return Opportunity::query()
            ->where('user_id', $user->id)
            ->whereIn('status', $this->activeOpportunityStatuses())
            ->latest('id')
            ->get();
    }

    /**
     * @return Collection<int, InvestmentSeat>
     */
    public function activePurchasedSeatsFor(User $user): Collection
    {
        return InvestmentSeat::query()
            ->with(['opportunity.user'])
            ->where('user_id', $user->id)
            ->whereHas('opportunity', fn ($query) => $query->whereIn('status', $this->activeOpportunityStatuses()))
            ->latest('id')
            ->get();
    }

    /**
     * @return Collection<int, InterestRequest>
     */
    public function activeInterestRequestsFor(User $user): Collection
    {
        return InterestRequest::query()
            ->with(['opportunity.user', 'investmentSeat'])
            ->where('user_id', $user->id)
            ->whereHas('opportunity', fn ($query) => $query->whereIn('status', $this->activeOpportunityStatuses()))
            ->latest('id')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    private function activeOpportunityStatuses(): array
    {
        return [
            OpportunityStatus::Published->value,
            OpportunityStatus::Reserved->value,
        ];
    }
}
