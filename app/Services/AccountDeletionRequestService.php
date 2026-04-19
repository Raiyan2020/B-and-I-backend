<?php

namespace App\Services;

use App\Enums\AccountDeletionRequestStatus;
use App\Enums\NotificationCategory;
use App\Enums\OpportunityStatus;
use App\Models\AccountDeletionRequest;
use App\Models\Admin;
use App\Models\FcmToken;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\Devices\DeviceService;
use App\Services\Notifications\GeneralNotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountDeletionRequestService
{
    public function __construct(
        private readonly GeneralNotificationService $generalNotificationService,
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

            $this->generalNotificationService->sendToUser(
                $user,
                new GeneralNotification(
                    title: [
                        'ar' => 'تمت الموافقة على طلب حذف الحساب',
                        'en' => 'Your account deletion request was approved',
                    ],
                    body: [
                        'ar' => 'تمت الموافقة على طلب حذف حسابك، وتم تسجيل خروجك من جميع الأجهزة.',
                        'en' => 'Your account deletion request was approved and you have been logged out from all devices.',
                    ],
                    notificationType: 'account_deletion_request.approved',
                    category: NotificationCategory::System,
                    model: $accountDeletionRequest,
                    payload: [
                        'account_deletion_request_id' => $accountDeletionRequest->id,
                    ],
                )
            );

            $accountDeletionRequest->update([
                'status' => AccountDeletionRequestStatus::Approved,
                'rejection_reason' => null,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

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

            $user = $accountDeletionRequest->user()->lockForUpdate()->firstOrFail();

            $accountDeletionRequest->update([
                'status' => AccountDeletionRequestStatus::Rejected,
                'rejection_reason' => $reason,
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $this->generalNotificationService->sendToUser(
                $user,
                new GeneralNotification(
                    title: [
                        'ar' => 'تم رفض طلب حذف الحساب',
                        'en' => 'Your account deletion request was rejected',
                    ],
                    body: [
                        'ar' => 'تم رفض طلب حذف الحساب. يمكنك مراجعة سبب الرفض داخل التطبيق.',
                        'en' => 'Your account deletion request was rejected. You can review the rejection reason in the app.',
                    ],
                    notificationType: 'account_deletion_request.rejected',
                    category: NotificationCategory::System,
                    model: $accountDeletionRequest,
                    payload: [
                        'account_deletion_request_id' => $accountDeletionRequest->id,
                        'rejection_reason' => $reason,
                    ],
                )
            );

            return $accountDeletionRequest->fresh(['user', 'reviewer']);
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
