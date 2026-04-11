<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\AuthServiceInterface;
use App\DTO\Auth\LoginDTO;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    use ResponseTrait;
    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request->validated());
        $result = $this->service->login($dto);

        if ($result['status'] === 'invalid_credentials') {
            return $this->jsonResponse(
                msg: __('apis.invalid_credentials'),
                code: 422,
                error: true,
            );
        }

        if ($result['status'] === 'email_unverified') {
            return $this->jsonResponse(
                msg: __('apis.email_verification_required'),
                code: 403,
                error: true,
                key: 'need_active',
                data: $result['data'],
            );
        }

        return $this->jsonResponse(data: UserResource::make($result['user'])->setToken($result['token']));
    }
}
