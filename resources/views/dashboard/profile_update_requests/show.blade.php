<x-dashboard.layouts.master title="{{ __('dashboard.profile_update_request_details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.profile_update_request_details') }}">
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
                                <h4 class="card-title mb-0">{{ __('dashboard.profile_update_request_details') }}</h4>
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

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ __('dashboard.profile_update_request') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered comparison-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.field') }}</th>
                                            <th>{{ __('dashboard.old_data') }}</th>
                                            <th>{{ __('dashboard.new_data') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comparisonRows as $item)
                                            <tr class="{{ $item['changed'] ? 'changed-row' : 'unchanged-row' }}">
                                                <td class="font-weight-bold field-cell">
                                                    {{ $item['label'] }}
                                                    @if($item['changed'])
                                                        <span class="badge badge-warning ml-50 changed-badge">{{ __('dashboard.changed_field') }}</span>
                                                    @endif
                                                </td>
                                                <td class="{{ $item['changed'] ? 'old-value-cell' : '' }}">
                                                    @if($item['type'] === 'file' && $item['old_url'])
                                                        @if($item['key'] === 'image')
                                                            <div class="file-preview">
                                                                <img src="{{ $item['old_url'] }}" alt="{{ $item['label'] }}">
                                                            </div>
                                                        @endif
                                                        <a href="{{ $item['old_url'] }}" target="_blank">{{ __('dashboard.show') }}</a>
                                                    @else
                                                        {{ $item['old_display'] }}
                                                    @endif
                                                </td>
                                                <td class="{{ $item['changed'] ? 'new-value-cell' : '' }}">
                                                    @if($item['type'] === 'file' && $item['new_url'])
                                                        @if($item['key'] === 'image')
                                                            <div class="file-preview">
                                                                <img src="{{ $item['new_url'] }}" alt="{{ $item['label'] }}">
                                                            </div>
                                                        @endif
                                                        @if($item['changed'])
                                                            <div class="updated-value-label">New</div>
                                                        @endif
                                                        <a href="{{ $item['new_url'] }}" target="_blank">{{ __('dashboard.show') }}</a>
                                                    @else
                                                        @if($item['changed'])
                                                            <div class="updated-value-label">New</div>
                                                        @endif
                                                        {{ $item['new_display'] }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if($row->status === \App\Enums\ProfileUpdateRequestStatus::Pending)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">{{ __('dashboard.review_request') }}</h4>
                            </div>
                            <div class="card-body">
                                <form class="form form-vertical store" method="POST"
                                      action="{{ route('admin.profile-update-requests.review', $row) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.request_status') }}</label>
                                                <select class="form-control" name="status" id="review-status" required>
                                                    <option value="{{ \App\Enums\ProfileUpdateRequestStatus::Approved->value }}">{{ __('dashboard.approve_request') }}</option>
                                                    <option value="{{ \App\Enums\ProfileUpdateRequestStatus::Rejected->value }}">{{ __('dashboard.reject_request') }}</option>
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
                                                        <a href="{{ route('admin.profile-update-requests.show', $historyItem) }}"
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
                    rejectionWrapper.classList.toggle('d-none', statusSelect.value !== '{{ \App\Enums\ProfileUpdateRequestStatus::Rejected->value }}');
                }

                statusSelect.addEventListener('change', toggleReason);
                toggleReason();
            });
        </script>
    @endpush

    @push('page-styles')
        <style>
            .comparison-table .changed-row td {
                background: rgba(255, 193, 7, 0.16);
            }

            .comparison-table td,
            .comparison-table th {
                vertical-align: middle;
            }

            .comparison-table .changed-row {
                box-shadow: inset 4px 0 0 #ff9f43;
            }

            .comparison-table .changed-row .field-cell {
                background: rgba(255, 159, 67, 0.18);
                border-right: 2px solid rgba(255, 159, 67, 0.32);
            }

            .comparison-table .changed-row .old-value-cell {
                color: #6c757d;
            }

            .comparison-table .changed-row .new-value-cell {
                background: rgba(40, 199, 111, 0.10);
                border: 2px solid rgba(40, 199, 111, 0.22);
                font-weight: 600;
            }

            .comparison-table .unchanged-row td {
                opacity: 0.78;
            }

            .changed-badge {
                font-size: 0.72rem;
            }

            .updated-value-label {
                display: inline-block;
                margin-bottom: 0.45rem;
                padding: 0.18rem 0.5rem;
                border-radius: 999px;
                background: rgba(40, 199, 111, 0.14);
                color: #1f8b4c;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
            }

            .file-preview {
                margin-bottom: 0.75rem;
            }

            .file-preview img {
                width: 90px;
                height: 90px;
                object-fit: cover;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
            }
        </style>
    @endpush
</x-dashboard.layouts.master>
