<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InterestRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InterestRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:interest-requests', ['only' => ['index', 'show']]);
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = InterestRequest::query()
                ->with(['opportunity.user', 'user', 'investmentSeat'])
                ->select('interest_requests.*')
                ->latest('id');

            return DataTables::eloquent($query)
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

        return view('dashboard.interest_requests.index');
    }

    public function show(InterestRequest $interestRequest): View
    {
        $interestRequest->load([
            'opportunity.user',
            'opportunity.category',
            'user',
            'investmentSeat',
        ]);

        return view('dashboard.interest_requests.show', [
            'row' => $interestRequest,
        ]);
    }
}
