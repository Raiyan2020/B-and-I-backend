<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\AuthServiceInterface;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json(['success' => true, 'message' => 'Logged out'], 200);
    }
}
