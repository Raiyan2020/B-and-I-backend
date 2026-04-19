<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LatestAccountDeletionRequestResource;
use App\Services\AccountDeletionRequestService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountDeletionRequestController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly AccountDeletionRequestService $accountDeletionRequestService) {}

    public function latest(Request $request): JsonResponse
    {
        $accountDeletionRequest = $this->accountDeletionRequestService->latestForUser($request->user());

        return $this->jsonResponse(
            data: $accountDeletionRequest
                ? LatestAccountDeletionRequestResource::make($accountDeletionRequest)
                : null,
        );
    }

    public function store(Request $request): JsonResponse
    {
        $result = $this->accountDeletionRequestService->submit($request->user());

        if (($result['status'] ?? null) === 'pending_request_exists') {
            return $this->jsonResponse(
                msg: __('apis.account_deletion_request_already_pending'),
                code: 422,
                error: true,
                data: isset($result['request'])
                    ? LatestAccountDeletionRequestResource::make($result['request'])
                    : null,
            );
        }

        return $this->jsonResponse(
            msg: __('apis.account_deletion_request_submitted'),
            code: 201,
            data: LatestAccountDeletionRequestResource::make($result['request']),
        );
    }
}
