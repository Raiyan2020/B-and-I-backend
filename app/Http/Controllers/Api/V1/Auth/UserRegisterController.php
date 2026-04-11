<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\DTO\Auth\RegisterInvestorDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAdvertiserRequest;
use App\Http\Requests\RegisterInvestorRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthServiceInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterController extends Controller
{
    use ResponseTrait;

    public function __construct(private AuthServiceInterface $service)
    {
    }

    public function investorRegister(RegisterInvestorRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $dto = RegisterInvestorDTO::fromRequest($validated);
                $user = $this->service->registerInvestor($dto);
                return $this->jsonResponse(
                    msg: __('auth.register_success'),
                    code: Response::HTTP_CREATED,
                    data: UserResource::make($user),
                    key: 'need_active'
                );
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(msg: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR, error: true);
        }

    }

    public function advertiserRegister(RegisterAdvertiserRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $file = $request->file('company_license');
                $dto = RegisterAdvertiserDTO::fromRequest($validated, $file);
                $user = $this->service->registerAdvertiser($dto);
                return $this->jsonResponse(
                    msg: __('auth.register_success'),
                    code: Response::HTTP_CREATED,
                    data: UserResource::make($user),
                    key: 'need_active'
                );
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(msg: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR, error: true);
        }
    }
}
