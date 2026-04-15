<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthServiceInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly AuthServiceInterface $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = User::query()
            ->with(['preferredSector', 'category'])
            ->findOrFail($request->user()->id);

        return $this->jsonResponse(
            data: UserResource::make($user),
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $result = $this->service->updateProfile($request->user(), $request->validated());

        if (($result['status'] ?? null) === 'pending_request_exists') {
            return $this->jsonResponse(
                msg: __('apis.profile_update_request_already_pending'),
                code: 422,
                error: true,
                data: UserResource::make($result['user']),
            );
        }

        return $this->jsonResponse(
            data: UserResource::make($result['user']),
            msg: __('apis.profile_update_sent_for_review'),
        );
    }
}
