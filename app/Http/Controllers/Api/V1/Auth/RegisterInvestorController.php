<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterInvestorRequest;
use App\Services\Auth\AuthServiceInterface;
use App\DTO\Auth\RegisterInvestorDTO;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;

class RegisterInvestorController extends Controller
{
    use ResponseTrait;
    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(RegisterInvestorRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $dto = RegisterInvestorDTO::fromRequest($validated);
                $result = $this->service->registerInvestor($dto);
                return $this->jsonResponse(data: UserResource::make($result['user'])->setToken($result['token']), code: Response::HTTP_CREATED);
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(msg: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR, error: true);
        }

    }
}
