<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User',
            'data' => new UserResource($request->user()),
        ], 200);
    }
}
