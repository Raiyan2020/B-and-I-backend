<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\OpportunityStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\InterestRequests\AwardInterestRequest;
use App\Models\InterestRequest;
use App\Services\Opportunity\OpportunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InterestRequestController extends Controller
{
    public function __construct(private readonly OpportunityService $opportunityService)
    {
        $this->middleware('permission:interest-requests', ['only' => ['index']]);
        $this->middleware('permission:show-interest-request', ['only' => ['show']]);
        $this->middleware('permission:award-interest-request', ['only' => ['award']]);
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = InterestRequest::query()
                ->with(['opportunity.user', 'user', 'investmentSeat'])
                ->select('interest_requests.*')
                ->when(request()->filled('opportunity_id'), function ($builder) {
                    $builder->where('opportunity_id', request()->integer('opportunity_id'));
                })
                ->latest('id');

            return DataTables::eloquent($query)
                ->order(function () {})
                ->addColumn('opportunity_id', function (InterestRequest $interestRequest): int|string {
                    return $interestRequest->opportunity_id ?? '';
                })
                ->addColumn('opportunity_name', function (InterestRequest $interestRequest): string {
                    return $interestRequest->opportunity?->company_name ?? '-';
                })
                ->addColumn('advertiser_name', function (InterestRequest $interestRequest): string {
                    return $interestRequest->opportunity?->user?->name ?: '-';
                })
                ->addColumn('investor_name', function (InterestRequest $interestRequest): string {
                    return $interestRequest->user?->name ?: '-';
                })
                ->addColumn('seat_reference', function (InterestRequest $interestRequest): string {
                    return '#' . ($interestRequest->investment_seat_id ?? '-');
                })
                ->editColumn('created_at', function (InterestRequest $interestRequest): string {
                    return $interestRequest->created_at
                        ? $interestRequest->created_at
                            ->timezone(config('app.timezone'))
                            ->locale(app()->getLocale())
                            ->translatedFormat('d M Y - h:i A')
                        : '-';
                })
                ->make(true);
        }

        return view('dashboard.interest_requests.index', [
            'opportunityId' => request('opportunity_id'),
        ]);
    }

    public function show(InterestRequest $interestRequest): View
    {
        $interestRequest->load([
            'opportunity.user',
            'opportunity.category',
            'opportunity.investor',
            'user',
            'investmentSeat.user',
            'investmentSeat.opportunity',
        ]);

        return view('dashboard.interest_requests.show', [
            'row' => $interestRequest,
            'awardStatuses' => [
                OpportunityStatus::Published->value => __('dashboard.opportunity_status_published'),
                OpportunityStatus::Reserved->value => __('dashboard.opportunity_status_reserved'),
                OpportunityStatus::Completed->value => __('dashboard.opportunity_status_completed'),
            ],
        ]);
    }

    public function award(AwardInterestRequest $request, InterestRequest $interestRequest): JsonResponse
    {
        $this->opportunityService->awardInterestRequest(
            admin: auth('admin')->user(),
            interestRequest: $interestRequest,
            status: OpportunityStatus::from($request->validated('status')),
        );

        return response()->json([
            'key' => 'success',
            'msg' => __('dashboard.opportunity_awarded_successfully'),
            'url' => route('admin.interest-requests.show', $interestRequest),
        ]);
    }
}
