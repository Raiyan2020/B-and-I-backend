<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Http\Resources\AdLightResource;
use App\Http\Resources\InterestRequestResource;
use App\Http\Resources\InvestmentSeatResource;
use App\Models\GeneralSetting;
use App\Models\Opportunity;
use App\Services\Opportunity\OpportunityService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly OpportunityService $service) {}

    public function index(Request $request): JsonResponse
    {
        $request->attributes->set('seat_price', GeneralSetting::getValueForKey('seat_price'));

        $user = $request->user('sanctum') ?? auth('sanctum')->user();

        $opportunities = Opportunity::query()
            ->with('category')
            ->whereIn('status', [
                OpportunityStatus::Published->value,
                OpportunityStatus::Reserved->value,
            ])
            ->when($user?->role === UserRole::Investor, function ($query) use ($user) {
                $query->with([
                    'investmentSeats' => fn($seatQuery) => $seatQuery
                        ->select(['id', 'opportunity_id', 'user_id'])
                        ->where('user_id', $user->id),
                    'interestRequests' => fn($interestQuery) => $interestQuery
                        ->select(['id', 'opportunity_id', 'user_id', 'investment_seat_id'])
                        ->where('user_id', $user->id),
                ]);
            })
            ->latest()
            ->paginate(15);

        return $this->jsonResponse(data: [
            'opportunities' => AdLightResource::collection($opportunities->items())->resolve($request),
            'pagination'    => [
                'current_page' => $opportunities->currentPage(),
                'last_page'    => $opportunities->lastPage(),
                'per_page'     => $opportunities->perPage(),
                'total'        => $opportunities->total(),
            ]
        ]);
    }

    public function show(Request $request, Opportunity $opportunity): JsonResponse
    {
        $request->attributes->set('seat_price', GeneralSetting::getValueForKey('seat_price'));

        abort_unless(
            in_array(($opportunity->status?->value ?? $opportunity->status), [
                OpportunityStatus::Published->value,
                OpportunityStatus::Reserved->value,
            ], true),
            404
        );

        $user = $request->user('sanctum') ?? auth('sanctum')->user();

        $opportunity->load('category');

        $opportunity->load([
            'investmentSeats' => fn($seatQuery) => $seatQuery
                ->select(['id', 'opportunity_id', 'user_id'])
                ->where('user_id', $user->id),
            'interestRequests' => fn($interestQuery) => $interestQuery
                ->select(['id', 'opportunity_id', 'user_id', 'investment_seat_id'])
                ->where('user_id', $user->id),
        ]);


        return $this->jsonResponse(data: AdResource::make($opportunity));
    }

    public function purchaseSeat(Request $request, Opportunity $opportunity): JsonResponse
    {
        $request->attributes->set('seat_price', GeneralSetting::getValueForKey('seat_price'));

        $seat = $this->service->purchaseSeat($request->user(), $opportunity);

        $opportunity->load([
            'category',
            'investmentSeats' => fn($seatQuery) => $seatQuery
                ->select(['id', 'opportunity_id', 'user_id'])
                ->where('user_id', $request->user()->id),
            'interestRequests' => fn($interestQuery) => $interestQuery
                ->select(['id', 'opportunity_id', 'user_id', 'investment_seat_id'])
                ->where('user_id', $request->user()->id),
        ]);

        return $this->jsonResponse(
            msg: __('apis.seat_purchased_successfully'),
            code: 201,
            data: [
                'seat' => InvestmentSeatResource::make($seat),
                'opportunity' => AdResource::make($opportunity),
            ],
        );
    }

    public function submitInterest(Request $request, Opportunity $opportunity): JsonResponse
    {
        $request->attributes->set('seat_price', GeneralSetting::getValueForKey('seat_price'));

        $interestRequest = $this->service->submitInterest($request->user(), $opportunity);

        $opportunity->load([
            'category',
            'investmentSeats' => fn($seatQuery) => $seatQuery
                ->select(['id', 'opportunity_id', 'user_id'])
                ->where('user_id', $request->user()->id),
            'interestRequests' => fn($interestQuery) => $interestQuery
                ->select(['id', 'opportunity_id', 'user_id', 'investment_seat_id'])
                ->where('user_id', $request->user()->id),
        ]);

        return $this->jsonResponse(
            msg: __('apis.interest_submitted_successfully'),
            code: 201,
            data: [
                'interest_request' => InterestRequestResource::make($interestRequest),
                'opportunity' => AdResource::make($opportunity),
            ],
        );
    }
}
