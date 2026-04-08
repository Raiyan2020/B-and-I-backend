<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTO\Auth\RegisterAdvertiserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAdvertiserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthServiceInterface;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterAdvertiserController extends Controller
{
    use ResponseTrait;
    public function __construct(private AuthServiceInterface $service) {}

    public function __invoke(RegisterAdvertiserRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $file = $request->file('company_license');
                $dto = RegisterAdvertiserDTO::fromRequest($validated, $file);
                $user = $this->service->registerAdvertiser($dto);
                return $this->jsonResponse(
                    data: UserResource::make($user),
                    code: Response::HTTP_CREATED,
                    msg: __('auth.register_success')
                );
            });
        } catch (\Exception $e) {
            return $this->jsonResponse(msg: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR, error: true);
        }
    }
}
