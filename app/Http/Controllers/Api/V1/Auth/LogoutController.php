<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\AuthServiceInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    use ResponseTrait;

    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $this->service->logout(
            $request->user(),
            $request->input('device_token'),
        );

        return $this->jsonResponse(
            msg: __('apis.logged_out_successfully'),
        );
    }
}
