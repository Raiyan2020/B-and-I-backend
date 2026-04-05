<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterInvestorRequest;
use App\Services\Auth\AuthServiceInterface;
use App\DTO\Auth\RegisterInvestorDTO;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class RegisterInvestorController extends Controller
{
    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(RegisterInvestorRequest $request): JsonResponse
    {
        $dto = RegisterInvestorDTO::fromRequest($request->validated());
        $result = $this->service->registerInvestor($dto);

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
