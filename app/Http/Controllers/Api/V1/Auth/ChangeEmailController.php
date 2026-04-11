<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestCurrentEmailChangeOtpRequest;
use App\Http\Requests\RequestNewEmailChangeOtpRequest;
use App\Http\Requests\VerifyCurrentEmailChangeOtpRequest;
use App\Http\Requests\VerifyNewEmailChangeOtpRequest;
use App\Services\Auth\EmailChangeService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class ChangeEmailController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly EmailChangeService $service,
    ) {}

    public function requestCurrent(RequestCurrentEmailChangeOtpRequest $request): JsonResponse
    {
        $result = $this->service->requestCurrentEmailOtp(
            $request->user(),
            $request->validated('current_password'),
        );

        if ($result['status'] === 'current_password_invalid') {
            return $this->jsonResponse(
                msg: __('apis.current_password_incorrect'),
                code: 422,
                error: true,
            );
        }

        return $this->jsonResponse(msg: __('apis.current_email_change_code_sent'));
    }

    public function verifyCurrent(VerifyCurrentEmailChangeOtpRequest $request): JsonResponse
    {
        $result = $this->service->verifyCurrentEmailOtp(
            $request->user(),
            $request->validated('otp'),
        );

        if ($result['status'] === 'invalid') {
            return $this->jsonResponse(
                msg: __('apis.verification_code_invalid'),
                code: 422,
                error: true,
            );
        }

        return $this->jsonResponse(msg: __('apis.current_email_verified_for_change'));
    }

    public function requestNew(RequestNewEmailChangeOtpRequest $request): JsonResponse
    {
        $result = $this->service->requestNewEmailOtp(
            $request->user(),
            $request->validated('email'),
        );

        if ($result['status'] === 'current_email_verification_required') {
            return $this->jsonResponse(
                msg: __('apis.email_change_current_verification_required'),
                code: 422,
                error: true,
            );
        }

        if ($result['status'] === 'same_email') {
            return $this->jsonResponse(
                msg: __('apis.email_change_same_email'),
                code: 422,
                error: true,
            );
        }

        return $this->jsonResponse(msg: __('apis.new_email_change_code_sent'));
    }

    public function verifyNew(VerifyNewEmailChangeOtpRequest $request): JsonResponse
    {
        $result = $this->service->verifyNewEmailOtp(
            $request->user(),
            $request->validated('email'),
            $request->validated('otp'),
        );

        if ($result['status'] === 'current_email_verification_required') {
            return $this->jsonResponse(
                msg: __('apis.email_change_current_verification_required'),
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

        return $this->jsonResponse(msg: __('apis.email_changed_successfully_logged_out'));
    }
}
