<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\RequestForgotPasswordOtpRequest;
use App\Http\Requests\ResetForgotPasswordRequest;
use App\Http\Requests\VerifyForgotPasswordOtpRequest;
use App\Services\Auth\AuthServiceInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class UserPasswordController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly AuthServiceInterface $service) {}

    public function requestForgotPasswordOtp(RequestForgotPasswordOtpRequest $request): JsonResponse
    {
        $result = $this->service->requestForgotPasswordOtp(
            $request->validated('email'),
        );

        if ($result['status'] === 'account_not_found') {
            return $this->jsonResponse(
                msg: __('apis.account_not_found'),
                code: 404,
                error: true,
            );
        }

        return $this->jsonResponse(
            msg: __('apis.forgot_password_code_sent'),
        );
    }

    public function verifyForgotPasswordOtp(VerifyForgotPasswordOtpRequest $request): JsonResponse
    {
        $result = $this->service->verifyForgotPasswordOtp(
            $request->validated('email'),
            $request->validated('otp'),
        );

        if ($result['status'] === 'invalid') {
            return $this->jsonResponse(
                msg: __('apis.verification_code_invalid'),
                code: 422,
                error: true,
            );
        }

        return $this->jsonResponse(
            msg: __('apis.forgot_password_code_verified'),
        );
    }

    public function resetForgottenPassword(ResetForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->service->resetForgottenPassword(
            $request->validated('email'),
            $request->validated('password'),
        );

        if ($result['status'] === 'verification_required') {
            return $this->jsonResponse(
                msg: __('apis.forgot_password_verification_required'),
                code: 422,
                error: true,
            );
        }

        return $this->jsonResponse(
            msg: __('apis.password_reset_successfully_login_again'),
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $result = $this->service->changePassword(
            $request->user(),
            $request->validated('current_password'),
            $request->validated('password'),
        );

        if ($result['status'] === 'current_password_invalid') {
            return $this->jsonResponse(
                msg: __('apis.current_password_incorrect'),
                code: 422,
                error: true,
            );
        }

        return $this->jsonResponse(
            msg: __('apis.password_updated_logged_out_all_devices'),
        );
    }
}
