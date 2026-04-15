<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LatestProfileUpdateRequestResource;
use App\Services\ProfileUpdateRequestService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileUpdateRequestController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly ProfileUpdateRequestService $profileUpdateRequestService) {}

    public function latest(Request $request): JsonResponse
    {
        $profileUpdateRequest = $this->profileUpdateRequestService->latestForUser($request->user());

        return $this->jsonResponse(
            data: $profileUpdateRequest
                ? LatestProfileUpdateRequestResource::make($profileUpdateRequest)
                : null,
        );
    }
}
