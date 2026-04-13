<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InvestmentSeat;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvestmentSeatController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:investment-seats', ['only' => ['index', 'show']]);
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = InvestmentSeat::query()
                ->with(['opportunity.user', 'user'])
                ->select('investment_seats.*')
                ->latest('id');

            return DataTables::eloquent($query)
                ->addColumn('opportunity_name', function (InvestmentSeat $seat): string {
                    return $seat->opportunity?->company_name ?? '-';
                })
                ->addColumn('advertiser_name', function (InvestmentSeat $seat): string {
                    return $seat->opportunity?->user?->name ?: '-';
                })
                ->addColumn('investor_name', function (InvestmentSeat $seat): string {
                    return $seat->user?->name ?: '-';
                })
                ->editColumn('price_paid', function (InvestmentSeat $seat): string {
                    return number_format((float) ($seat->price_paid ?? 0), 2);
                })
                ->editColumn('purchased_at', function (InvestmentSeat $seat): string {
                    $date = $seat->purchased_at ?? $seat->created_at;

                    return $date
                        ? $date->timezone(config('app.timezone'))
                            ->locale(app()->getLocale())
                            ->translatedFormat('d M Y - h:i A')
                        : '-';
                })
                ->make(true);
        }

        return view('dashboard.investment_seats.index');
    }

    public function show(InvestmentSeat $investmentSeat): View
    {
        $investmentSeat->load([
            'opportunity.user',
            'opportunity.category',
            'user',
            'interestRequests.user',
        ]);

        return view('dashboard.investment_seats.show', [
            'row' => $investmentSeat,
        ]);
    }
}
