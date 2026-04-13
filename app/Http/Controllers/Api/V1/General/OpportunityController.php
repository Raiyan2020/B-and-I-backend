<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Http\Resources\AdLightResource;
use App\Models\GeneralSetting;
use App\Models\Opportunity;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    use ResponseTrait;

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
                    'investmentSeats' => fn ($seatQuery) => $seatQuery
                        ->select(['id', 'opportunity_id', 'user_id'])
                        ->where('user_id', $user->id),
                    'interestRequests' => fn ($interestQuery) => $interestQuery
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

        if ($user?->role === UserRole::Investor) {
            $opportunity->load([
                'investmentSeats' => fn ($seatQuery) => $seatQuery
                    ->select(['id', 'opportunity_id', 'user_id'])
                    ->where('user_id', $user->id),
                'interestRequests' => fn ($interestQuery) => $interestQuery
                    ->select(['id', 'opportunity_id', 'user_id', 'investment_seat_id'])
                    ->where('user_id', $user->id),
            ]);
        }

        return $this->jsonResponse(data: AdResource::make($opportunity));
    }
}
