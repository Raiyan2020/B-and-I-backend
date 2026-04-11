<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function __invoke(Request $request): JsonResponse
    {
        $user = User::query()
            ->with(['preferredSector', 'category'])
            ->findOrFail($request->user()->id);

        return $this->jsonResponse(
            data: UserResource::make($user),
        );
    }
}
