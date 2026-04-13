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
                    <div class="card pb-1">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center flex-wrap">
                                <h4 class="card-title mb-0 mr-1">{{ __('dashboard.opportunity_details') }}</h4>
                                @if($row->opportunity)
                                    <span class="badge badge-light-primary badge-pill px-2 py-1">
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
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.company_name') }}:</strong> {{ $row->opportunity->company_name ?: '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.advertiser') }}:</strong> {{ $row->opportunity->user?->name ?: '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.goal') }}:</strong> {{ $row->opportunity->goal ? __('dashboard.goal_'.$row->opportunity->goal->value) : '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.table status') }}:</strong> {{ $row->opportunity->status ? __('dashboard.opportunity_status_'.$row->opportunity->status->value) : '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.category') }}:</strong> {{ $row->opportunity->category?->getTranslation('name', app()->getLocale()) ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.investment_required') }}:</strong> {{ $row->opportunity->investment_required ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.sale_percentage') }}:</strong> {{ $row->opportunity->sale_percentage ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.contact_email') }}:</strong> {{ $row->opportunity->contact_email ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.current_awarded_investor') }}:</strong> {{ $row->opportunity->investor?->name ?: '-' }}</div>
                                    </div>
                                @else
                                    <p class="mb-0">{{ __('dashboard.unavailable') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card pb-1">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center flex-wrap">
                                <h4 class="card-title mb-0 mr-1">{{ __('dashboard.investment_seat_details') }}</h4>
                                @if($row->investmentSeat)
                                    <span class="badge badge-light-primary badge-pill px-2 py-1">
                                        #{{ $row->investmentSeat->id }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center mt-1 mt-md-0">
                                @if($row->investmentSeat)
                                    <a class="btn btn-sm btn-outline-primary mr-1" href="{{ route('admin.investment-seats.show', $row->investmentSeat) }}">
                                        {{ __('dashboard.show') }}
                                    </a>
                                @endif
                                <button
                                    class="btn btn-sm btn-primary"
                                    type="button"
                                    data-toggle="collapse"
                                    data-target="#linked-seat-summary"
                                    aria-expanded="false"
                                    aria-controls="linked-seat-summary"
                                >
                                    {{ __('dashboard.View More') }}
                                </button>
                            </div>
                        </div>
                        <div id="linked-seat-summary" class="collapse">
                            <div class="card-body border-top">
                                @if($row->investmentSeat)
                                    <div class="row">
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.investor') }}:</strong> {{ $row->investmentSeat->user?->name ?: '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.table email') }}:</strong> {{ $row->investmentSeat->user?->email ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.price_paid') }}:</strong> {{ number_format((float) ($row->investmentSeat->price_paid ?? 0), 2) }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.purchased_at') }}:</strong> {{ ($row->investmentSeat->purchased_at ?? $row->investmentSeat->created_at)?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</div>
                                        <div class="col-md-4 mb-2"><strong>{{ __('dashboard.opportunity_reference') }}:</strong> #{{ $row->investmentSeat->opportunity?->opportunity_number ?? $row->investmentSeat->opportunity_id }}</div>
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
                            <h4 class="card-title">{{ __('dashboard.interest_request_details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>#{{ $row->id }}</strong></div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.submitted_at') }}:</strong> {{ $row->created_at?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.investor') }}:</strong> {{ $row->user?->name ?: '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.table email') }}:</strong> {{ $row->user?->email ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.seat_reference') }}:</strong> #{{ $row->investment_seat_id }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.opportunity_reference') }}:</strong> #{{ $row->opportunity?->opportunity_number ?? $row->opportunity_id }}</div>
                            </div>

                            <hr>

                            <form class="form form-vertical store" method="POST" action="{{ route('admin.interest-requests.award', $row) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.award_status') }}</label>
                                            <select class="form-control" name="status" required>
                                                @foreach($awardStatuses as $value => $label)
                                                    <option value="{{ $value }}" {{ ($row->opportunity?->status?->value ?? null) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary submit_button">{{ __('dashboard.award_investment') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
