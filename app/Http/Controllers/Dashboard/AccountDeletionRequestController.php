<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\AccountDeletionRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AccountDeletionRequests\ReviewRequest;
use App\Models\AccountDeletionRequest;
use App\Services\AccountDeletionRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AccountDeletionRequestController extends Controller
{
    public function __construct(private readonly AccountDeletionRequestService $accountDeletionRequestService)
    {
        $this->middleware('permission:users', ['only' => ['show']]);
        $this->middleware('permission:edit-user', ['only' => ['review']]);
    }

    public function show(AccountDeletionRequest $accountDeletionRequest): View
    {
        $accountDeletionRequest->load('reviewer');
        $user = $accountDeletionRequest->user()->withTrashed()->firstOrFail();

        return view('dashboard.account_deletion_requests.show', [
            'row' => $accountDeletionRequest,
            'user' => $user,
            'listTitle' => $user->isInvestor()
                ? __('dashboard.investors_list')
                : __('dashboard.advertisers_companies_list'),
            'indexRouteName' => $user->isInvestor()
                ? 'admin.investors.index'
                : 'admin.advertisers.index',
            'activeAdvertisements' => $this->accountDeletionRequestService->activeAdvertisementsFor($user),
            'activePurchasedSeats' => $this->accountDeletionRequestService->activePurchasedSeatsFor($user),
            'activeInterestRequests' => $this->accountDeletionRequestService->activeInterestRequestsFor($user),
            'history' => $user->accountDeletionRequests()
                ->whereKeyNot($accountDeletionRequest->id)
                ->latest()
                ->get(),
        ]);
    }

    public function review(ReviewRequest $request, AccountDeletionRequest $accountDeletionRequest): JsonResponse
    {
        $status = AccountDeletionRequestStatus::from($request->validated('status'));

        if ($status === AccountDeletionRequestStatus::Approved) {
            $this->accountDeletionRequestService->approve(auth('admin')->user(), $accountDeletionRequest);
        } else {
            $this->accountDeletionRequestService->reject(
                auth('admin')->user(),
                $accountDeletionRequest,
                (string) $request->validated('rejection_reason')
            );
        }

        return response()->json([
            'key' => 'success',
            'msg' => __('dashboard.account_deletion_request_review_saved'),
            'url' => route('admin.account-deletion-requests.show', $accountDeletionRequest),
        ]);
    }
}
