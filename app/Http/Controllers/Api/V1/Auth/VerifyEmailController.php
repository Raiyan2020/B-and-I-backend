<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly EmailVerificationService $service) {}

    public function __invoke(Request $request, string $id, string $hash): JsonResponse
    {
        $result = $this->service->verify($id, $hash, $request->hasValidSignature());

        if ($result['status'] === 'invalid') {
            return $this->jsonResponse(
                msg: __('apis.verification_link_invalid'),
                code: 422,
                error: true,
            );
        }

        if ($result['status'] === 'already_verified') {
            return $this->jsonResponse(msg: __('apis.already_verified'));
        }

        return $this->jsonResponse(msg: __('apis.email_verified_successfully'));
    }
}
