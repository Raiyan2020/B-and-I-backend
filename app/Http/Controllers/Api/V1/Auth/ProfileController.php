<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function __invoke(Request $request): JsonResponse
    {
        return $this->jsonResponse(
            data: UserResource::make($request->user()->loadMissing(['preferredSector', 'category'])),
        );
    }
}
