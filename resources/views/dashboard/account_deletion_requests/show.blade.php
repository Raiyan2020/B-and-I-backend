<x-dashboard.layouts.master title="{{ __('dashboard.account_deletion_request_details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.account_deletion_request_details') }}">
                <li class="breadcrumb-item">
                    <a href="{{ route($indexRouteName) }}">{{ $listTitle }}</a>
                </li>
            </x-dashboard.layouts.breadcrumb>

            @php
                $statusBadgeClass = match($row->status?->value) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                };
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h4 class="card-title mb-0">{{ __('dashboard.account_deletion_request_details') }}</h4>
                                <p class="text-muted mb-0 mt-50">{{ $user->name }}</p>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="badge badge-{{ $statusBadgeClass }} mr-1">
                                    {{ __('dashboard.'.($row->status?->value ?? 'pending')) }}
                                </span>
                                <a href="{{ route($indexRouteName) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-arrow-right mr-50"></i>{{ __('dashboard.back') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.request_status') }}</small>
                                    <strong>{{ __('dashboard.'.($row->status?->value ?? 'pending')) }}</strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.request_submitted_at') }}</small>
                                    <strong>{{ $row->created_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.reviewed_at') }}</small>
                                    <strong>{{ $row->reviewed_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.reviewed_by') }}</small>
                                    <strong>{{ $row->reviewer?->name ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.user_type') }}</small>
                                    <strong>
                                        {{ $user->isInvestor() ? __('dashboard.investor') : __('dashboard.advertiser') }}
                                    </strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.table email') }}</small>
                                    <strong>{{ $user->email ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.full_phone') }}</small>
                                    <strong>{{ $user->full_phone ?: '-' }}</strong>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <small class="text-muted d-block">{{ __('dashboard.account_status') }}</small>
                                    <strong>
                                        {{ $user->trashed() ? __('dashboard.account_deleted') : __('dashboard.active') }}
                                    </strong>
                                </div>
                                @if($row->rejection_reason)
                                    <div class="col-12">
                                        <div class="alert alert-danger mb-0">
                                            <strong>{{ __('dashboard.rejection_reason') }}:</strong>
                                            {{ $row->rejection_reason }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->isCompany())
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">{{ __('dashboard.active_advertisements') }}</h4>
                                <span class="badge badge-primary">{{ $activeAdvertisements->count() }}</span>
                            </div>
                            <div class="card-body">
                                @if($activeAdvertisements->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                    <th>{{ __('dashboard.company_name') }}</th>
                                                    <th>{{ __('dashboard.status') }}</th>
                                                    <th>{{ __('dashboard.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activeAdvertisements as $advertisement)
                                                    <tr>
                                                        <td>#{{ $advertisement->id }}</td>
                                                        <td>{{ $advertisement->opportunity_number ?? '-' }}</td>
                                                        <td>{{ $advertisement->company_name ?? '-' }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $advertisement->status?->value === 'reserved' ? 'info' : 'success' }}">
                                                                {{ __('dashboard.opportunity_status_'.($advertisement->status?->value ?? 'pending')) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.opportunities.show', $advertisement) }}"
                                                               class="btn btn-sm btn-outline-info">
                                                                <i class="feather icon-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">{{ __('dashboard.no_active_advertisements') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">{{ __('dashboard.active_purchased_seats') }}</h4>
                            <span class="badge badge-primary">{{ $activePurchasedSeats->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($activePurchasedSeats->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.price_paid') }}</th>
                                                <th>{{ __('dashboard.purchased_at') }}</th>
                                                <th>{{ __('dashboard.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activePurchasedSeats as $seat)
                                                <tr>
                                                    <td>#{{ $seat->id }}</td>
                                                    <td>{{ $seat->opportunity?->opportunity_number ?? '-' }}</td>
                                                    <td>{{ $seat->opportunity?->company_name ?? '-' }}</td>
                                                    <td>{{ $seat->price_paid ?? '-' }}</td>
                                                    <td>{{ $seat->purchased_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.investment-seats.show', $seat) }}"
                                                           class="btn btn-sm btn-outline-info">
                                                            <i class="feather icon-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('dashboard.no_active_purchased_seats') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">{{ __('dashboard.active_interest_requests') }}</h4>
                            <span class="badge badge-primary">{{ $activeInterestRequests->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($activeInterestRequests->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.seat_reference') }}</th>
                                                <th>{{ __('dashboard.submitted_at') }}</th>
                                                <th>{{ __('dashboard.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeInterestRequests as $interestRequest)
                                                <tr>
                                                    <td>#{{ $interestRequest->id }}</td>
                                                    <td>{{ $interestRequest->opportunity?->opportunity_number ?? '-' }}</td>
                                                    <td>{{ $interestRequest->opportunity?->company_name ?? '-' }}</td>
                                                    <td>#{{ $interestRequest->investment_seat_id ?? '-' }}</td>
                                                    <td>{{ $interestRequest->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.interest-requests.show', $interestRequest) }}"
                                                           class="btn btn-sm btn-outline-info">
                                                            <i class="feather icon-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('dashboard.no_active_interest_requests') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if($row->status === \App\Enums\AccountDeletionRequestStatus::Pending)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">{{ __('dashboard.review_request') }}</h4>
                            </div>
                            <div class="card-body">
                                <form class="form form-vertical store" method="POST"
                                      action="{{ route('admin.account-deletion-requests.review', $row) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.request_status') }}</label>
                                                <select class="form-control" name="status" id="review-status" required>
                                                    <option value="{{ \App\Enums\AccountDeletionRequestStatus::Approved->value }}">{{ __('dashboard.approve_request') }}</option>
                                                    <option value="{{ \App\Enums\AccountDeletionRequestStatus::Rejected->value }}">{{ __('dashboard.reject_request') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 d-none" id="rejection-reason-wrapper">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.rejection_reason') }}</label>
                                                <textarea class="form-control" name="rejection_reason" rows="4"
                                                          placeholder="{{ __('dashboard.enter_rejection_reason') }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary submit_button">{{ __('dashboard.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($history->isNotEmpty())
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">{{ __('dashboard.request_history') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.request_status') }}</th>
                                                <th>{{ __('dashboard.request_submitted_at') }}</th>
                                                <th>{{ __('dashboard.reviewed_at') }}</th>
                                                <th>{{ __('dashboard.rejection_reason') }}</th>
                                                <th>{{ __('dashboard.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($history as $historyItem)
                                                <tr>
                                                    <td>#{{ $historyItem->id }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ match($historyItem->status?->value) {
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            default => 'warning',
                                                        } }}">
                                                            {{ __('dashboard.'.($historyItem->status?->value ?? 'pending')) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $historyItem->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                    <td>{{ $historyItem->reviewed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                    <td>{{ $historyItem->rejection_reason ?: '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.account-deletion-requests.show', $historyItem) }}"
                                                           class="btn btn-sm btn-outline-info">
                                                            <i class="feather icon-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('page-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var statusSelect = document.getElementById('review-status');
                var rejectionWrapper = document.getElementById('rejection-reason-wrapper');

                if (!statusSelect || !rejectionWrapper) {
                    return;
                }

                function toggleReason() {
                    rejectionWrapper.classList.toggle('d-none', statusSelect.value !== '{{ \App\Enums\AccountDeletionRequestStatus::Rejected->value }}');
                }

                statusSelect.addEventListener('change', toggleReason);
                toggleReason();
            });
        </script>
    @endpush
</x-dashboard.layouts.master>
