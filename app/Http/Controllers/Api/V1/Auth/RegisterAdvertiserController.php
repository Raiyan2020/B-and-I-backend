<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAdvertiserRequest;
use App\Services\Auth\AuthServiceInterface;
use App\DTO\Auth\RegisterAdvertiserDTO;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class RegisterAdvertiserController extends Controller
{
    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(RegisterAdvertiserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = $request->file('company_license');
        $dto = RegisterAdvertiserDTO::fromRequest($validated, $file);
        $result = $this->service->registerAdvertiser($dto);

        return response()->json([
            'success' => true,
            'message' => 'Account created. Please verify your email.',
            'data' => [
                'token' => $result['token'],
                'user' => new UserResource($result['user']),
            ],
        ], 201);
    }
}
