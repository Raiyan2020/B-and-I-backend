<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\OpportunityStatus;
use App\Http\Requests\Dashboard\Opportunities\ReviewRequest;
use App\Models\Opportunity;
use App\Services\Opportunity\OpportunityService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OpportunityController extends AdminBasicController
{
    public function __construct(private readonly OpportunityService $opportunityService)
    {
        // $this->middleware('permission:opportunities', ['only' => ['index', 'show']]);
        // $this->middleware('permission:review-opportunity', ['only' => ['review']]);

        $this->model = Opportunity::class;
        $this->directoryName = 'opportunities';
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->opportunityService->dashboardIndex(
                (new QueryOptions())->latest()
            );

            return DataTables::of($rows)
                ->editColumn('created_at', function (Opportunity $opportunity) {
                    return $opportunity->created_at
                        ? $opportunity->created_at
                            ->timezone(config('app.timezone'))
                            ->locale(app()->getLocale())
                            ->translatedFormat('d M Y - h:i A')
                        : '-';
                })
                ->make(true);
        }

        return view('dashboard.opportunities.index');
    }

    public function show($id): View
    {
        return view('dashboard.opportunities.show', [
            'row' => $this->opportunityService->findForDashboard((int) $id),
            'reviewStatuses' => [
                OpportunityStatus::Approved->value => __('dashboard.opportunity_status_approved'),
                OpportunityStatus::NeedsModification->value => __('dashboard.opportunity_status_needs_modification'),
            ],
        ]);
    }

    public function review(ReviewRequest $request, Opportunity $opportunity): JsonResponse
    {
        $status = OpportunityStatus::from($request->validated('status'));
        $this->opportunityService->reviewByAdmin(
            admin: auth('admin')->user(),
            opportunity: $opportunity,
            status: $status,
            reviewNote: $request->validated('review_note'),
        );

        return response()->json([
            'key' => 'success',
            'msg' => __('dashboard.opportunity_review_saved'),
            'url' => route('admin.opportunities.show', $opportunity),
        ]);
    }
}
