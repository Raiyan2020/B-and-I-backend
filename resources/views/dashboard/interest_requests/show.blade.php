<x-dashboard.layouts.master title="{{ __('dashboard.interest_request_details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.interest_request_details') }}">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.interest-requests.index') }}">{{ __('dashboard.interest_requests_list') }}</a>
                </li>
            </x-dashboard.layouts.breadcrumb>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('dashboard.interest_request_details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>#{{ $row->id }}</strong></div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.submitted_at') }}:</strong> {{ $row->created_at?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.opportunity_reference') }}:</strong> {{ $row->opportunity?->company_name ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.category') }}:</strong> {{ $row->opportunity?->category?->getTranslation('name', app()->getLocale()) ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.advertiser') }}:</strong> {{ $row->opportunity?->user?->name ?: '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.investor') }}:</strong> {{ $row->user?->name ?: '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.table email') }}:</strong> {{ $row->user?->email ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.seat_reference') }}:</strong> #{{ $row->investment_seat_id }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.price_paid') }}:</strong> {{ number_format((float) ($row->investmentSeat?->price_paid ?? 0), 2) }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.purchased_at') }}:</strong> {{ ($row->investmentSeat?->purchased_at ?? $row->investmentSeat?->created_at)?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
