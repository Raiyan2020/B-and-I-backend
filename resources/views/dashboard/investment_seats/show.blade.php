<x-dashboard.layouts.master title="{{ __('dashboard.investment_seat_details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.investment_seat_details') }}">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.investment-seats.index') }}">{{ __('dashboard.investment_seats_list') }}</a>
                </li>
            </x-dashboard.layouts.breadcrumb>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center flex-wrap">
                                <h4 class="card-title mb-0 mr-1">{{ __('dashboard.opportunity_details') }}</h4>
                                @if($row->opportunity)
                                    <span class="badge badge-primary badge-pill px-2 py-1">
                                        #{{ $row->opportunity->opportunity_number ?? $row->opportunity->id }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center mt-1 mt-md-0">
                                @if($row->opportunity)
                                    <a class="btn btn-sm btn-outline-primary mr-1" href="{{ route('admin.opportunities.show', $row->opportunity) }}">
                                        {{ __('dashboard.show') }}
                                    </a>
                                @endif
                                <button
                                    class="btn btn-sm btn-primary"
                                    type="button"
                                    data-toggle="collapse"
                                    data-target="#linked-opportunity-summary"
                                    aria-expanded="false"
                                    aria-controls="linked-opportunity-summary"
                                >
                                    {{ __('dashboard.View More') }}
                                </button>
                            </div>
                        </div>
                        <div id="linked-opportunity-summary" class="collapse">
                            <div class="card-body border-top">
                                @if($row->opportunity)
                                    <div class="row">
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.opportunity_reference') }}:</strong> #{{ $row->opportunity->opportunity_number ?? $row->opportunity->id }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.company_name') }}:</strong> {{ $row->opportunity->company_name ?: '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.advertiser') }}:</strong> {{ $row->opportunity->user?->name ?: '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.goal') }}:</strong> {{ $row->opportunity->goal ? __('dashboard.goal_'.$row->opportunity->goal->value) : '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.table status') }}:</strong> {{ $row->opportunity->status ? __('dashboard.opportunity_status_'.$row->opportunity->status->value) : '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.category') }}:</strong> {{ $row->opportunity->category?->getTranslation('name', app()->getLocale()) ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.investment_required') }}:</strong> {{ $row->opportunity->investment_required ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.sale_percentage') }}:</strong> {{ $row->opportunity->sale_percentage ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.contact_email') }}:</strong> {{ $row->opportunity->contact_email ?? '-' }}</div>
                                    </div>
                                @else
                                    <p class="mb-0">{{ __('dashboard.unavailable') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('dashboard.investment_seat_details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>#{{ $row->id }}</strong></div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.opportunity_reference') }}:</strong> {{ $row->opportunity?->company_name ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.advertiser') }}:</strong> {{ $row->opportunity?->user?->name ?: '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.investor') }}:</strong> {{ $row->user?->name ?: '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.table email') }}:</strong> {{ $row->user?->email ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.category') }}:</strong> {{ $row->opportunity?->category?->getTranslation('name', app()->getLocale()) ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.price_paid') }}:</strong> {{ number_format((float) ($row->price_paid ?? 0), 2) }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.purchased_at') }}:</strong> {{ ($row->purchased_at ?? $row->created_at)?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('dashboard.linked_interest_requests') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive overflow-auto">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text text-center">
                                            <th>#</th>
                                            <th>{{ __('dashboard.investor') }}</th>
                                            <th>{{ __('dashboard.table email') }}</th>
                                            <th>{{ __('dashboard.submitted_at') }}</th>
                                            <th>{{ __('dashboard.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text text-center">
                                        @forelse($row->interestRequests as $interestRequest)
                                            <tr>
                                                <td>{{ $interestRequest->id }}</td>
                                                <td>{{ $interestRequest->user?->name ?: '-' }}</td>
                                                <td>{{ $interestRequest->user?->email ?? '-' }}</td>
                                                <td>{{ $interestRequest->created_at?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.interest-requests.show', $interestRequest) }}">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">-</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
