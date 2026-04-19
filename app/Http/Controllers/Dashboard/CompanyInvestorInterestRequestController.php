<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyInvestorInterestRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CompanyInvestorInterestRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:company-investor-interest-requests', ['only' => ['index']]);
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = CompanyInvestorInterestRequest::query()
                ->with(['company', 'investor'])
                ->select('company_investor_interest_requests.*')
                ->when(request()->filled('company_id'), function ($builder) {
                    $builder->where('company_id', request()->integer('company_id'));
                })
                ->when(request()->filled('company_name'), function ($builder) {
                    $builder->whereHas('company', function ($companyQuery) {
                        $value = '%'.trim((string) request('company_name')).'%';

                        $companyQuery->where(function ($nestedQuery) use ($value) {
                            $nestedQuery
                                ->where('first_name', 'like', $value)
                                ->orWhere('last_name', 'like', $value)
                                ->orWhere('display_name', 'like', $value)
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$value]);
                        });
                    });
                })
                ->when(request()->filled('investor_name'), function ($builder) {
                    $builder->whereHas('investor', function ($investorQuery) {
                        $value = '%'.trim((string) request('investor_name')).'%';

                        $investorQuery->where(function ($nestedQuery) use ($value) {
                            $nestedQuery
                                ->where('first_name', 'like', $value)
                                ->orWhere('last_name', 'like', $value)
                                ->orWhere('display_name', 'like', $value)
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$value]);
                        });
                    });
                })
                ->when(request()->filled('interest_date'), function ($builder) {
                    $builder->whereDate('created_at', request('interest_date'));
                })
                ->latest('id');

            return DataTables::eloquent($query)
                ->addColumn('company_name', fn (CompanyInvestorInterestRequest $row): string => $row->company?->name ?: '-')
                ->addColumn('company_show_url', fn (CompanyInvestorInterestRequest $row): string => route('admin.users.show', $row->company_id))
                ->addColumn('investor_name', fn (CompanyInvestorInterestRequest $row): string => $row->investor?->name ?: '-')
                ->addColumn('investor_show_url', fn (CompanyInvestorInterestRequest $row): string => route('admin.users.show', $row->investor_id))
                ->editColumn('created_at', function (CompanyInvestorInterestRequest $row): string {
                    return $row->created_at
                        ? $row->created_at
                            ->timezone(config('app.timezone'))
                            ->locale(app()->getLocale())
                            ->translatedFormat('d M Y - h:i A')
                        : '-';
                })
                ->make(true);
        }

        return view('dashboard.company_investor_interest_requests.index', [
            'companyId' => request('company_id'),
            'companyName' => request('company_name'),
            'investorName' => request('investor_name'),
            'interestDate' => request('interest_date'),
        ]);
    }
}
