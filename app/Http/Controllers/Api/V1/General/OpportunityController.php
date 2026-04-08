<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicOpportunityResource;
use App\Models\Opportunity;
use App\Services\Opportunity\OpportunityService;
use App\Support\QueryOptions;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;

class OpportunityController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly OpportunityService $service) {}

    public function index(): JsonResponse
    {
        return $this->jsonResponse(data: PublicOpportunityResource::collection(
            $this->service->listApproved((new QueryOptions())->paginateNum(12))
        ));
    }

    public function show(Opportunity $opportunity): JsonResponse
    {
        abort_unless(($opportunity->status?->value ?? $opportunity->status) === 'approved', 404);

        return $this->jsonResponse(data: PublicOpportunityResource::make($opportunity->load(['category', 'user'])));
    }
}
