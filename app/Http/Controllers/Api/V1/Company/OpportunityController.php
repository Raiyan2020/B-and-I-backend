<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Opportunities\ListCompanyOpportunitiesRequest;
use App\Http\Requests\Api\V1\Opportunities\StoreOpportunityRequest;
use App\Http\Requests\Api\V1\Opportunities\UpdateOpportunityRequest;
use App\Http\Resources\OpportunityListResource;
use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Services\Opportunity\OpportunityService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OpportunityController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly OpportunityService $service) {}

    public function index(ListCompanyOpportunitiesRequest $request): JsonResponse
    {
        $paginator = $this->service->listForCompany($request->user(), $request->validated());

        return $this->jsonResponse(data: [
            'opportunities' => OpportunityListResource::collection($paginator->items())->resolve(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(StoreOpportunityRequest $request): JsonResponse
    {
        $opportunity = $this->service->createForCompany($request->user(), $request->validated());

        return $this->jsonResponse(
            data: OpportunityResource::make($opportunity->load('category')),
            msg: __('apis.opportunity_created_successfully'),
            code: Response::HTTP_CREATED,
        );
    }

    public function show(Opportunity $opportunity): JsonResponse
    {
        return $this->jsonResponse(data: OpportunityResource::make(
            $this->service->showForCompany(request()->user(), $opportunity)
        ));
    }

    public function update(UpdateOpportunityRequest $request, Opportunity $opportunity): JsonResponse
    {
        $opportunity = $this->service->updateForCompany($request->user(), $opportunity, $request->validated());

        return $this->jsonResponse(
            data: OpportunityResource::make($opportunity),
            msg: __('apis.opportunity_updated_and_sent_for_review'),
        );
    }
}
