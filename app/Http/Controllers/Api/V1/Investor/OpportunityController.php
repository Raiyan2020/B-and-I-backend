<?php

namespace App\Http\Controllers\Api\V1\Investor;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdLightResource;
use App\Models\GeneralSetting;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    use ResponseTrait;

    public function purchasedSeats(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($response = $this->ensureInvestor($user)) {
            return $response;
        }

        return $this->paginatedResponse(
            $request,
            $user,
            $this->baseInvestorQuery($user)
                ->whereHas('investmentSeats', fn (Builder $query) => $query->where('user_id', $user->id))
                ->whereDoesntHave('interestRequests', fn (Builder $query) => $query->where('user_id', $user->id))
                ->orderByDesc(
                    InvestmentSeat::query()
                        ->select('created_at')
                        ->whereColumn('opportunity_id', 'opportunities.id')
                        ->where('user_id', $user->id)
                        ->latest('created_at')
                        ->limit(1)
                )
        );
    }

    public function sentInterests(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($response = $this->ensureInvestor($user)) {
            return $response;
        }

        return $this->paginatedResponse(
            $request,
            $user,
            $this->baseInvestorQuery($user)
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
        if ($response = $this->ensureInvestor($user)) {
            return $response;
        }

        return $this->paginatedResponse(
            $request,
            $user,
            $this->baseInvestorQuery($user)
                ->where('investor_id', $user->id)
                ->where('status', OpportunityStatus::Reserved->value)
                ->latest('reviewed_at')
        );
    }

    protected function paginatedResponse(Request $request, User $user, Builder $query): JsonResponse
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

    protected function baseInvestorQuery(User $user): Builder
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

    protected function ensureInvestor(User $user): ?JsonResponse
    {
        if ($user->role === UserRole::Investor) {
            return null;
        }

        return $this->jsonResponse(
            msg: __('apis.investor_only_action'),
            code: 403,
            error: true,
        );
    }

    protected function resolvePerPage(Request $request): int
    {
        return max(1, min((int) $request->query('per_page', 15), 100));
    }
}
