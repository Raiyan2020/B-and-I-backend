<x-dashboard.layouts.master title="{{ __('dashboard.opportunity_details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.opportunity_details') }}">
                <li class="breadcrumb-item"><a href="{{ route('admin.opportunities.index') }}">{{ __('dashboard.opportunities_list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @php
                            $statusBadgeClass = match($row->status?->value) {
                                'pending' => 'warning',
                                'needs_revision' => 'danger',
                                'published' => 'success',
                                'reserved' => 'info',
                                'completed' => 'secondary',
                                default => 'primary',
                            };
                            $statusValue = $row->status?->value;
                            $shouldShowDeal = in_array($statusValue, ['reserved', 'completed'], true);
                            $dealTitle = $statusValue === 'completed'
                                ? __('dashboard.winning_deal')
                                : __('dashboard.proposed_deal');
                            $dealInterestRequest = $row->getRelation('dealInterestRequest');
                        @endphp
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center flex-wrap">
                                <h4 class="card-title mb-0 mr-1">{{ __('dashboard.opportunity_details') }}</h4>
                                <span class="badge badge-primary badge-pill px-2 py-1 mr-1">
                                    #{{ $row->opportunity_number ?? $row->id }}
                                </span>
                                <span class="badge badge-{{ $statusBadgeClass }} badge-pill px-2 py-1">
                                    {{ __('dashboard.opportunity_status_'.$row->status->value) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.company_name') }}:</strong> {{ $row->company_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.goal') }}:</strong> {{ __('dashboard.goal_'.$row->goal->value) }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.category') }}:</strong> {{ $row->category?->getTranslation('name', app()->getLocale()) ?? '' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.contact_name') }}:</strong> {{ $row->contact_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.contact_phone') }}:</strong> {{ $row->contact_phone }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.contact_email') }}:</strong> {{ $row->contact_email }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.owner_name') }}:</strong> {{ $row->owner_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.admin_company_name') }}:</strong> {{ $row->admin_company_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.license_number') }}:</strong> {{ $row->license_number }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.business_age_years') }}:</strong> {{ $row->business_age_years }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.investment_required') }}:</strong> {{ $row->investment_required }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.business_stage') }}:</strong> {{ $row->business_stage }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.sale_percentage') }}:</strong> {{ $row->sale_percentage ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.legal_entity') }}:</strong> {{ $row->legal_entity }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.financial_status') }}:</strong> {{ $row->financial_status }}</div>
                                <div class="col-12 mb-2"><strong>{{ __('dashboard.investment_reason') }}:</strong><br>{{ $row->investment_reason }}</div>
                                <div class="col-12 mb-2"><strong>{{ __('dashboard.full_description') }}:</strong><br>{{ $row->full_description }}</div>
                                @if($row->review_note)
                                    <div class="col-12 mb-2"><strong>{{ __('dashboard.review_note') }}:</strong><br>{{ $row->review_note }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($shouldShowDeal)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="card-title mb-0">
                                    <i class="feather icon-award text-{{ $statusValue === 'completed' ? 'success' : 'info' }} mr-1"></i>
                                    {{ $dealTitle }}
                                </h4>
                                <span class="badge badge-{{ $statusValue === 'completed' ? 'success' : 'info' }} badge-pill px-2 py-1 mt-1 mt-sm-0">
                                    {{ __('dashboard.opportunity_status_'.$statusValue) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive overflow-auto">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr class="text text-center">
                                                <th>#</th>
                                                <th>{{ __('dashboard.investor') }}</th>
                                                <th>{{ __('dashboard.table email') }}</th>
                                                <th>{{ __('dashboard.table phone') }}</th>
                                                <th>{{ __('dashboard.seat_reference') }}</th>
                                                <th>{{ __('dashboard.price_paid') }}</th>
                                                <th>{{ __('dashboard.submitted_at') }}</th>
                                                <th>{{ __('dashboard.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text text-center">
                                            @if($dealInterestRequest)
                                                <tr>
                                                    <td>{{ $dealInterestRequest->id }}</td>
                                                    <td>{{ $dealInterestRequest->user?->name ?: '-' }}</td>
                                                    <td>{{ $dealInterestRequest->user?->email ?: '-' }}</td>
                                                    <td>{{ $dealInterestRequest->user?->full_phone ?: '-' }}</td>
                                                    <td>#{{ $dealInterestRequest->investment_seat_id }}</td>
                                                    <td>{{ number_format((float) ($dealInterestRequest->investmentSeat?->price_paid ?? 0), 2) }}</td>
                                                    <td>{{ $dealInterestRequest->created_at?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</td>
                                                    <td>
                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.interest-requests.show', $dealInterestRequest) }}">
                                                            <i class="feather icon-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="8">-</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h4 class="card-title mb-0">{{ __('dashboard.latest_investment_seats') }}</h4>
                            <a class="btn btn-sm btn-outline-primary mt-1 mt-lg-0" href="{{ route('admin.investment-seats.index', ['opportunity_id' => $row->id]) }}">
                                {{ __('dashboard.view_all') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive overflow-auto">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text text-center">
                                            <th>#</th>
                                            <th>{{ __('dashboard.investor') }}</th>
                                            <th>{{ __('dashboard.price_paid') }}</th>
                                            <th>{{ __('dashboard.purchased_at') }}</th>
                                            <th>{{ __('dashboard.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text text-center">
                                        @forelse($row->investmentSeats as $seat)
                                            <tr>
                                                <td>{{ $seat->id }}</td>
                                                <td>{{ $seat->user?->name ?: '-' }}</td>
                                                <td>{{ number_format((float) ($seat->price_paid ?? 0), 2) }}</td>
                                                <td>{{ ($seat->purchased_at ?? $seat->created_at)?->timezone(config('app.timezone'))->locale(app()->getLocale())->translatedFormat('d M Y - h:i A') ?? '-' }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.investment-seats.show', $seat) }}">
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

                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h4 class="card-title mb-0">{{ __('dashboard.latest_interest_requests') }}</h4>
                            <a class="btn btn-sm btn-outline-primary mt-1 mt-lg-0" href="{{ route('admin.interest-requests.index', ['opportunity_id' => $row->id]) }}">
                                {{ __('dashboard.view_all') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive overflow-auto">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text text-center">
                                            <th>#</th>
                                            <th>{{ __('dashboard.investor') }}</th>
                                            <th>{{ __('dashboard.seat_reference') }}</th>
                                            <th>{{ __('dashboard.submitted_at') }}</th>
                                            <th>{{ __('dashboard.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text text-center">
                                        @forelse($row->interestRequests as $interestRequest)
                                            <tr>
                                                <td>{{ $interestRequest->id }}</td>
                                                <td>{{ $interestRequest->user?->name ?: '-' }}</td>
                                                <td>#{{ $interestRequest->investment_seat_id }}</td>
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

                @can('review-opportunity')
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">{{ __('dashboard.review_opportunity') }}</h4></div>
                            <div class="card-body">
                                <form class="form form-vertical store" method="POST" action="{{ route('admin.opportunities.review', $row) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.table status') }}</label>
                                                <select class="form-control" name="status" required>
                                                    @foreach($reviewStatuses as $value => $label)
                                                        <option value="{{ $value }}" {{ $row->status->value === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.review_note') }}</label>
                                                <textarea class="form-control" name="review_note" rows="4">{{ $row->review_note }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary submit_button">{{ __('dashboard.save_review') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
