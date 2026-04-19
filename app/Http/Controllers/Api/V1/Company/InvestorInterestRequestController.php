<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Company\StoreCompanyInvestorInterestRequest;
use App\Models\User;
use App\Services\CompanyInvestorInterestRequestService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InvestorInterestRequestController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly CompanyInvestorInterestRequestService $service) {}

    public function store(StoreCompanyInvestorInterestRequest $request): JsonResponse
    {
        if ($request->user()->role !== UserRole::Advertiser) {
            return $this->jsonResponse(
                msg: __('apis.have_no_permission'),
                code: 403,
                error: true,
            );
        }

        $interestRequest = $this->service->create(
            $request->user(),
            (int) $request->validated('investor_id')
        );

        $interestRequest->load(['company', 'investor']);

        return $this->jsonResponse(
            msg: __('apis.company_investor_interest_request_created'),
            code: Response::HTTP_CREATED,
            data: [
                'id' => $interestRequest->id,
                'company' => [
                    'id' => $interestRequest->company?->id,
                    'name' => $interestRequest->company?->name,
                ],
                'investor' => [
                    'id' => $interestRequest->investor?->id,
                    'name' => $interestRequest->investor?->name,
                ],
                'submitted_at' => $interestRequest->created_at?->toDateTimeString(),
            ],
        );
    }
}
