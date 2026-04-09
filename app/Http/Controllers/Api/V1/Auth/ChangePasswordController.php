<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\Auth\AuthServiceInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class ChangePasswordController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly AuthServiceInterface $service) {}

    public function __invoke(ChangePasswordRequest $request): JsonResponse
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
            msg: __('apis.password_updated_successfully'),
        );
    }
}
