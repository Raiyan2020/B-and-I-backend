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

        return $this->jsonResponse(
            data: UserResource::make($result['user']),
            msg: $result['email_verification_sent']
                ? __('apis.profile_updated_verification_required')
                : __('apis.profile_updated_successfully'),
        );
    }
}
