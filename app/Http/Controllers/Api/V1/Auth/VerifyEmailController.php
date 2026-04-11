<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Auth\EmailVerificationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class VerifyEmailController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly EmailVerificationService $service,
        private readonly AuthServiceInterface $authService,
    ) {}

    public function __invoke(VerifyEmailRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->service->verifyOtp($validated['email'], $validated['password'], $validated['otp']);

        if ($result['status'] === 'invalid_credentials') {
            return $this->jsonResponse(
                msg: __('apis.invalid_credentials'),
                code: 422,
                error: true,
            );
        }

        if ($result['status'] === 'invalid') {
            return $this->jsonResponse(
                msg: __('apis.verification_code_invalid'),
                code: 422,
                error: true,
            );
        }

        if ($result['status'] === 'already_verified') {
            return $this->jsonResponse(msg: __('apis.already_verified'));
        }

        $user = $result['user'];
        $token = $this->authService->issueTokenForUser(
            $user,
            $validated['device_token'] ?? null,
            $validated['device_type'] ?? null,
        );

        return $this->jsonResponse(
            msg: __('apis.email_verified_successfully'),
            data: UserResource::make($user)->setToken($token),
        );
    }
}
