<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResendVerificationRequest;
use App\Services\Auth\EmailVerificationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class ResendVerificationController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly EmailVerificationService $service) {}

    public function __invoke(ResendVerificationRequest $request): JsonResponse
    {
        $result = $this->service->resendForRole(
            $request->validated('email'),
            UserRole::from($request->validated('role')),
        );

        if ($result['status'] === 'already_verified') {
            return $this->jsonResponse(msg: __('apis.already_verified'));
        }

        if ($result['status'] === 'throttled') {
            return $this->jsonResponse(
                msg: __('apis.verification_resend_throttled', ['seconds' => $result['retry_after']]),
                code: 429,
                error: true,
            );
        }

        return $this->jsonResponse(msg: __('apis.verification_email_sent'));
    }
}
