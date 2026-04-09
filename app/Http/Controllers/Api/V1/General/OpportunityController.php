<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Enums\OpportunityStatus;
use App\Facades\BaseService;
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

    public function __construct(private readonly OpportunityService $service)
    {
    }

    public function index(): JsonResponse
    {
        $options = (new QueryOptions())
            ->with(['category', 'user'])
            ->latest()
            ->conditions(['status' => OpportunityStatus::Approved]);
        $opportunities = BaseService::setModel(Opportunity::class)->limit($options);
        return $this->jsonResponse(data: [
            'opportunities' => PublicOpportunityResource::collection($opportunities),
            'pagination'    => [
                'current_page' => $opportunities->currentPage(),
                'last_page'    => $opportunities->lastPage(),
                'per_page'     => $opportunities->perPage(),
                'total'        => $opportunities->total(),
            ]
        ]);
    }

    public function show(Opportunity $opportunity): JsonResponse
    {
        abort_unless(($opportunity->status?->value ?? $opportunity->status) === 'approved', 404);

        return $this->jsonResponse(data: PublicOpportunityResource::make($opportunity->load(['category', 'user'])));
    }
}
