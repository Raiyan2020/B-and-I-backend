<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Opportunities\ListCompanyOpportunitiesRequest;
use App\Http\Requests\Api\V1\Opportunities\StoreOpportunityRequest;
use App\Http\Requests\Api\V1\Opportunities\UpdateOpportunityRequest;
use App\Http\Resources\AdLightResource;
use App\Http\Resources\AdResource;
use App\Http\Resources\OpportunityListResource;
use App\Http\Resources\OpportunityResource;
use App\Models\GeneralSetting;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\Opportunity\OpportunityService;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OpportunityController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly OpportunityService $service)
    {
    }

    public function index(ListCompanyOpportunitiesRequest $request): JsonResponse
    {
        $paginator = $this->service->listForCompany($request->user(), $request->validated());

        return $this->jsonResponse(data: [
            'opportunities' => OpportunityListResource::collection($paginator->items())->resolve(),
            'pagination'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function purchasedSeats(Request $request): JsonResponse
    {
        if ($response = $this->ensureAdvertiser($request->user())) {
            return $response;
        }

        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $paginator = $this->service->listWithPurchasedSeatsForCompany(
            $request->user(),
            (int) ($validated['per_page'] ?? 15),
        );

        return $this->jsonResponse(data: [
            'opportunities' => OpportunityListResource::collection($paginator->items())->resolve(),
            'pagination'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function sentInterests(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($response = $this->ensureAdvertiser($user)) {
            return $response;
        }

        return $this->paginatedBuyerResponse(
            $request,
            $user,
            $this->baseBuyerQuery($user)
                ->whereHas('interestRequests', fn (Builder $query) => $query->where('user_id', $user->id))
                ->whereNotIn('status', [OpportunityStatus::Completed->value, OpportunityStatus::Reserved->value])
                ->orderByDesc(
                    InterestRequest::query()
                        ->select('created_at')
                        ->whereColumn('opportunity_id', 'opportunities.id')
                        ->where('user_id', $user->id)
                        ->latest('created_at')
                        ->limit(1)
                )
        );
    }

    public function currentRequests(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($response = $this->ensureAdvertiser($user)) {
            return $response;
        }

        return $this->paginatedBuyerResponse(
            $request,
            $user,
            $this->baseBuyerQuery($user)
                ->where('investor_id', $user->id)
                ->where('status', OpportunityStatus::Reserved->value)
                ->latest('reviewed_at')
        );
    }

    public function store(StoreOpportunityRequest $request): JsonResponse
    {
        $this->authorize('create', Opportunity::class);
        $opportunity = $this->service->createForCompany($request->user(), $request->validated());

        return $this->jsonResponse(
            msg: __('apis.opportunity_created_successfully'),
            code: Response::HTTP_CREATED,
            data: OpportunityResource::make($opportunity->load('category')),
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
        $this->authorize('update', $opportunity);

        $opportunity = $this->service->updateForCompany($request->user(), $opportunity, $request->validated());
        $request->attributes->set('seat_price', GeneralSetting::getValueForKey('seat_price'));

        return $this->jsonResponse(
            msg: __('apis.opportunity_updated_and_sent_for_review'),
            data: (new AdResource(
                $opportunity->load('category')->loadCount(['investmentSeats', 'interestRequests'])
            ))->includeSectionB(),
        );
    }

    protected function paginatedBuyerResponse(Request $request, User $user, Builder $query): JsonResponse
    {
        $request->attributes->set('seat_price', GeneralSetting::getValueForKey('seat_price'));

        $paginator = $query->paginate($this->resolvePerPage($request));

        return $this->jsonResponse(data: [
            'opportunities' => AdLightResource::collection($paginator->items())->resolve($request),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    protected function baseBuyerQuery(User $user): Builder
    {
        return Opportunity::query()
            ->with('category')
            ->withCount(['investmentSeats', 'interestRequests'])
            ->with([
                'investmentSeats' => fn ($query) => $query
                    ->select(['id', 'opportunity_id', 'user_id'])
                    ->where('user_id', $user->id),
                'interestRequests' => fn ($query) => $query
                    ->select(['id', 'opportunity_id', 'user_id', 'investment_seat_id'])
                    ->where('user_id', $user->id),
            ]);
    }

    protected function ensureAdvertiser(User $user): ?JsonResponse
    {
        if ($user->role === UserRole::Advertiser) {
            return null;
        }

        return $this->jsonResponse(
            msg: __('apis.have_no_permission'),
            code: 403,
            error: true,
        );
    }

    protected function resolvePerPage(Request $request): int
    {
        return max(1, min((int) $request->query('per_page', 15), 100));
    }
}
