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
