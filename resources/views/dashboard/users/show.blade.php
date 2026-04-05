<x-dashboard.layouts.master title="{{ __('dashboard.customer details') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.customer details') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.users.index') }}">{{ __('dashboard.users list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="content-body">
                <!-- page user view start -->
                <section class="page-users-view">
                    <div class="row">
                        <!-- User Info Card -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">{{ __('dashboard.customer details') }}</h4>
                                    <div class="d-flex gap-2">
                                        @can('edit-user')
                                            <a href="{{ route('admin.users.edit', $row->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather icon-edit mr-1"></i>{{ __('dashboard.edit') }}
                                            </a>
                                        @endcan
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="feather icon-arrow-right mr-1"></i>{{ __('dashboard.back') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- User Image -->
                                        <div class="col-12 col-md-4 text-center mb-3">
                                            <div class="user-view-image">
                                                @if($row->image)
                                                    <img src="{{ $row->image }}"
                                                        class="user-avatar-shadow w-100 rounded mb-2"
                                                        alt="User Avatar"
                                                        style="max-width: 200px; height: 200px; object-fit: cover; border-radius: 50%;">
                                                @else
                                                    <div class="user-avatar-placeholder rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                        style="width: 200px; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 auto;">
                                                        <i class="feather icon-user text-white" style="font-size: 80px;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- User Details -->
                                        <div class="col-12 col-md-8">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-user text-primary mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table name') }}</p>
                                                            <h5 class="mb-0">{{ $row->name }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <i class="feather icon-mail text-info mr-2" style="font-size: 18px;"></i>
                                                            <div>
                                                                <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table email') }}</p>
                                                                <h5 class="mb-0">{{ $row->email ?? __('dashboard.not specified') }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column" style="gap: 0.5rem;">
                                                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary"
                                                                    data-toggle="modal" data-target="#sendNotificationModal"
                                                                    title="{{ __('dashboard.send_notification') }}"
                                                                    style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                <i class="feather icon-bell" style="font-size: 16px;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-icon btn-outline-success"
                                                                    data-toggle="modal" data-target="#chargeWalletModal"
                                                                    title="{{ __('dashboard.charge_wallet') }}"
                                                                    style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fa fa-money" style="font-size: 16px;"></i>
                                                            </button>
                                                            @can('block-user')
                                                                <button type="button" class="btn btn-sm btn-icon btn-outline-warning toggle-block-btn-show"
                                                                        data-url="{{ route('admin.users.toggleBlock', $row->id) }}"
                                                                        data-blocked="{{ $row->is_blocked ? '1' : '0' }}"
                                                                        title="{{ $row->is_blocked ? __('dashboard.unblock') : __('dashboard.block') }}"
                                                                        style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="feather icon-slash" style="font-size: 16px;"></i>
                                                                </button>
                                                            @endcan
                                                            @can('delete-user')
                                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-row-show"
                                                                        data-url="{{ route('admin.users.destroy', $row->id) }}"
                                                                        title="{{ __('dashboard.delete') }}"
                                                                        style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="feather icon-trash-2" style="font-size: 16px;"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-phone text-success mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table phone') }}</p>
                                                            <h5 class="mb-0">
                                                                @if($row->country_code)
                                                                    <span class="flag-icon flag-icon-{{ \App\Helpers\CountryHelper::getCountryByCode($row->country_code)['iso'] ?? 'sa' }} flag-icon-squared mr-1"></span>
                                                                @endif
                                                                {{ $row->country_code ?? '' }} {{ $row->phone ?? __('dashboard.not specified') }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-check-circle text-success mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.phone_activation_status') }}</p>
                                                            <h5 class="mb-0">
                                                                <span class="badge badge-{{ $row->is_active ? 'success' : 'warning' }}">
                                                                    {{ $row->is_active ? __('dashboard.activated') : __('dashboard.not_activated') }}
                                                                </span>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-slash text-danger mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table status') }}</p>
                                                            <h5 class="mb-0">
                                                                <span class="badge badge-{{ $row->is_blocked ? 'danger' : 'success' }}">
                                                                    {{ $row->is_blocked ? __('dashboard.blocked') : __('dashboard.un_blocked') }}
                                                                </span>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-calendar text-secondary mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.created at') }}</p>
                                                            <h5 class="mb-0">{{ $row->created_at ? $row->created_at->format('Y-m-d H:i') : __('dashboard.not specified') }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Balance Card -->
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="feather icon-wallet text-primary mr-1"></i>
                                        {{ __('dashboard.wallet_balance') }}
                                    </h4>
                                </div>
                                <div class="card-body text-center">
                                    <div class="wallet-balance-display">
                                        <h2 class="text-primary mb-2" id="wallet-balance-display" style="font-size: 2.5rem; font-weight: bold;">
                                            {{ $row?->wallet?->available_balance ?? 0 }}
                                            <span class="text-muted" style="font-size: 1.2rem;">{{ __('dashboard.currency') }}</span>
                                        </h2>
                                        <p class="text-muted mb-1">{{ __('dashboard.current_balance') }}</p>
                                        <div id="reserved-balance-display">
                                            <p class="text-warning mb-0" style="font-size: 0.875rem;">
                                                {{ __('dashboard.reserved_balance') }}:
                                                <strong id="reserved-balance-value">{{ $row?->wallet?->reserved_balance ?? 0 }}</strong>
                                                {{ __('dashboard.currency') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Card -->
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="feather icon-bar-chart-2 text-info mr-1"></i>
                                        {{ __('dashboard.statistics') }}
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-2">
                                            <div class="stat-item">
                                                <h4 class="text-primary mb-0">{{ $row->orders_count ?? 0 }}</h4>
                                                <p class="text-muted mb-0" style="font-size: 0.875rem;">{{ __('dashboard.total_orders') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="stat-item">
                                                <h4 class="text-success mb-0">{{ $row->completed_orders_count ?? 0 }}</h4>
                                                <p class="text-muted mb-0" style="font-size: 0.875rem;">{{ __('dashboard.completed_orders') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="stat-item">
                                                <h4 class="text-warning mb-0">{{ $row->pending_orders_count ?? 0 }}</h4>
                                                <p class="text-muted mb-0" style="font-size: 0.875rem;">{{ __('dashboard.pending_orders') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="stat-item">
                                                <h4 class="text-info mb-0" id="statistics-wallet-transactions-count">{{ $row->walletTransactions->count() ?? $row->wallet_transactions_count ?? 0 }}</h4>
                                                <p class="text-muted mb-0" style="font-size: 0.875rem;">{{ __('dashboard.wallet_transactions') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Wallet Transactions -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="feather icon-credit-card text-success mr-1"></i>
                                        {{ __('dashboard.recent_wallet_transactions') }}
                                    </h4>
                                    <span class="badge badge-primary" id="wallet-transactions-count">{{ $row->wallet_transactions_count ?? 0 }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id="transactions-table-wrapper" style="{{ (isset($row?->walletTransactions) && $row?->walletTransactions?->count() > 0) ? '' : 'display: none;' }}">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('dashboard.transaction_type') }}</th>
                                                    <th>{{ __('dashboard.amount') }}</th>
                                                    <th>{{ __('dashboard.balance_after') }}</th>
                                                    <th>{{ __('dashboard.date') }}</th>
                                                    <th>{{ __('dashboard.status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="wallet-transactions-tbody">
                                                    @foreach($row->walletTransactions?->take(10) as $transaction)
                                                        @php
                                                            // Get type value - handle both enum object and raw value
                                                            if ($transaction->type instanceof \App\Enums\WalletTransactionTypeEnum) {
                                                                $typeValue = $transaction->type->value;
                                                            } elseif (is_object($transaction->type) && isset($transaction->type->value)) {
                                                                $typeValue = $transaction->type->value;
                                                            } else {
                                                                $typeValue = (int) $transaction->type;
                                                            }

                                                            $typeObj = \App\Enums\WalletTransactionTypeEnum::getFullObj($typeValue);
                                                            $isCharge = $typeValue === \App\Enums\WalletTransactionTypeEnum::CHARGE->value;
                                                            $isPayment = $typeValue === \App\Enums\WalletTransactionTypeEnum::PAYMENT->value;
                                                            $isTransfer = $typeValue === \App\Enums\WalletTransactionTypeEnum::TRANSFER->value;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span class="badge badge-{{ $isCharge ? 'success' : ($isPayment ? 'danger' : 'warning') }}">
                                                                    {{ $typeObj['label'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <strong class="text-{{ $isCharge ? 'success' : ($isPayment ? 'danger' : 'warning') }}">
                                                                    {{ $isCharge ? '+' : '-' }}{{ $transaction?->amount }} {{ __('dashboard.currency') }}
                                                                </strong>
                                                            </td>
                                                            <td>{{ $transaction?->balance_after ?? 0 }} {{ __('dashboard.currency') }}</td>
                                                            <td>{{ $transaction?->created_at ? $transaction?->created_at->format('Y-m-d H:i') : '-' }}</td>
                                                            <td>
                                                                <span class="badge badge-success">{{ __('dashboard.completed') }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    <div class="text-center py-4" id="no-transactions-message" style="{{ (isset($row?->walletTransactions) && $row?->walletTransactions?->count() > 0) ? 'display: none;' : '' }}">
                                        <i class="feather icon-inbox text-muted" style="font-size: 48px;"></i>
                                        <p class="text-muted mt-2">{{ __('dashboard.no_wallet_transactions') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="feather icon-shopping-cart text-primary mr-1"></i>
                                        {{ __('dashboard.recent_orders') }}
                                    </h4>
                                    <a href="#" class="btn btn-sm btn-outline-primary">{{-- {{ route('admin.orders.index', ['user_id' => $row->id]) }}--}}
                                        {{ __('dashboard.view_all') }}
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if(isset($row?->orders) && $row?->orders && $row?->orders?->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('dashboard.order_number') }}</th>
                                                        <th>{{ __('dashboard.order status') }}</th>
                                                        <th>{{ __('dashboard.total price') }}</th>
                                                        <th>{{ __('dashboard.date') }}</th>
                                                        <th>{{ __('dashboard.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($row?->orders?->take(10) as $order)
                                                        <tr>
                                                            <td>#{{ $order->id }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $order?->status === 'completed' ? 'success' : ($order?->status === 'pending' ? 'warning' : 'danger') }}">
                                                                    {{ __('dashboard.' . $order?->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $order?->total_price ?? 0 }} {{ __('dashboard.currency') }}</td>
                                                            <td>{{ $order?->created_at ? $order?->created_at->format('Y-m-d H:i') : '-' }}</td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-outline-primary">{{-- {{ route('admin.orders.show', $order?->id) }} --}}
                                                                    <i class="feather icon-eye"></i>

                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="feather icon-shopping-cart text-muted" style="font-size: 48px;"></i>
                                            <p class="text-muted mt-2">{{ __('dashboard.no_orders') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- page user view end -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    @push('vendor-styles')
        <!-- Flag Icon CSS -->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/fonts/flag-icon-css/css/flag-icon.min.css') }}">
    @endpush

    @push('page-scripts')
        <script src="{{ asset('dashboardAssets/custom/js/shared/table-toggle-block.js') }}"></script>
        <script src="{{ asset('dashboardAssets/custom/js/shared/table-delete-row.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Handle charge wallet form submission via AJAX
                $('#charge-wallet-form').on('submit', function(e) {
                    e.preventDefault();

                    var $form = $(this);
                    var $submitBtn = $('#charge-submit-btn');
                    var $spinner = $submitBtn.find('.spinner-border');
                    var url = $form.attr('action');
                    var formData = $form.serialize();

                    // Disable submit button and show spinner
                    $submitBtn.prop('disabled', true);
                    $spinner.removeClass('d-none');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.key === 'success') {
                                // Update wallet balance
                                $('#wallet-balance-display').html(response.balance + ' <span class="text-muted" style="font-size: 1.2rem;">{{ __('dashboard.currency') }}</span>');
                                $('#current-balance-value').text(response.balance + ' {{ __('dashboard.currency') }}');

                                // Update reserved balance
                                $('#reserved-balance-value').text(response.reserved_balance || 0);

                                // Update transactions table
                                if (response.transactions && response.transactions.length > 0) {
                                    var tbody = $('#wallet-transactions-tbody');
                                    var html = '';

                                    response.transactions.forEach(function(trans) {
                                        var isCharge = trans.type === 1;
                                        var badgeClass = isCharge ? 'success' : (trans.type === 2 ? 'danger' : 'warning');
                                        var textClass = isCharge ? 'success' : (trans.type === 2 ? 'danger' : 'warning');
                                        var sign = isCharge ? '+' : '-';

                                        html += '<tr>' +
                                            '<td><span class="badge badge-' + badgeClass + '">' + trans.type_label + '</span></td>' +
                                            '<td><strong class="text-' + textClass + '">' + sign + trans.amount + ' {{ __('dashboard.currency') }}</strong></td>' +
                                            '<td>' + trans.balance_after + ' {{ __('dashboard.currency') }}</td>' +
                                            '<td>' + trans.created_at + '</td>' +
                                            '<td><span class="badge badge-success">{{ __('dashboard.completed') }}</span></td>' +
                                            '</tr>';
                                    });

                                    tbody.html(html);
                                    $('#no-transactions-message').hide();
                                    $('#transactions-table-wrapper').show();

                                    // Update transactions count in both places
                                    var transactionsCount = response.transactions.length;
                                    $('#wallet-transactions-count').text(transactionsCount);
                                    $('#statistics-wallet-transactions-count').text(transactionsCount);
                                }

                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('dashboard.success') }}',
                                    text: response.msg,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reset form and close modal
                                $form[0].reset();
                                $('#chargeWalletModal').modal('hide');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('dashboard.error') }}',
                                    text: response.msg
                                });
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = '{{ __('dashboard.something_went_wrong') }}';
                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                errorMsg = xhr.responseJSON.msg;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('dashboard.error') }}',
                                text: errorMsg
                            });
                        },
                        complete: function() {
                            // Re-enable submit button and hide spinner
                            $submitBtn.prop('disabled', false);
                            $spinner.addClass('d-none');
                        }
                    });
                });
            });

            // Handle toggle block button click on show page (reload page after success)
            $(document).on('click', '.toggle-block-btn-show', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var toggleUrl = $btn.data('url');
                var isBlocked = $btn.data('blocked') == 1;
                var blockText = isBlocked ? '{{ __('dashboard.un_block') }}' : '{{ __('dashboard.block') }}';

                Swal.fire({
                    title: '{{ __('dashboard.confirm') }}',
                    text: '{{ __('dashboard.toggle_block_text') }}' + ' (' + blockText + ')',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('dashboard.yes') }}',
                    cancelButtonText: '{{ __('dashboard.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: toggleUrl,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.key === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('dashboard.success') }}',
                                        text: response.msg,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('dashboard.error') }}',
                                        text: response.msg
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('dashboard.error') }}',
                                    text: xhr.responseJSON?.msg || '{{ __('dashboard.something_went_wrong') }}'
                                });
                            }
                        });
                    }
                });
            });

            // Handle delete button click on show page (redirect to index after success)
            $(document).on('click', '.delete-row-show', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var deleteUrl = $btn.data('url');

                Swal.fire({
                    title: '{{ __('dashboard.confirm') }}',
                    text: '{{ __('dashboard.are_you_sure') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('dashboard.yes') }}',
                    cancelButtonText: '{{ __('dashboard.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.key === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('dashboard.success') }}',
                                        text: response.msg,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = '{{ route('admin.users.index') }}';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('dashboard.error') }}',
                                        text: response.msg
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('dashboard.error') }}',
                                    text: xhr.responseJSON?.msg || '{{ __('dashboard.something_went_wrong') }}'
                                });
                            }
                        });
                    }
                });
            });

            // Initialize toggle block for show page
            if (window.ToggleBlock) {
                window.ToggleBlock.init({
                    tableSelector: null, // Not needed for show page
                    csrfToken: $('meta[name="csrf-token"]').attr('content')
                });
            }

            // Handle toggle block button click on show page (reload page after success)
            $(document).on('click', '.toggle-block-btn-show', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var toggleUrl = $btn.data('url');
                var isBlocked = $btn.data('blocked') == 1;
                var blockText = isBlocked ? '{{ __('dashboard.un_block') }}' : '{{ __('dashboard.block') }}';

                Swal.fire({
                    title: '{{ __('dashboard.confirm') }}',
                    text: '{{ __('dashboard.toggle_block_text') }}' + ' (' + blockText + ')',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('dashboard.yes') }}',
                    cancelButtonText: '{{ __('dashboard.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: toggleUrl,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.key === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('dashboard.success') }}',
                                        text: response.msg,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('dashboard.error') }}',
                                        text: response.msg
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('dashboard.error') }}',
                                    text: xhr.responseJSON?.msg || '{{ __('dashboard.something_went_wrong') }}'
                                });
                            }
                        });
                    }
                });
            });

            // Handle delete button click on show page (redirect to index after success)
            $(document).on('click', '.delete-row-show', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var deleteUrl = $btn.data('url');

                Swal.fire({
                    title: '{{ __('dashboard.confirm') }}',
                    text: '{{ __('dashboard.delete_confirmation') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __('dashboard.yes') }}',
                    cancelButtonText: '{{ __('dashboard.cancel') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.key === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('dashboard.success') }}',
                                        text: response.msg,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = '{{ route('admin.users.index') }}';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('dashboard.error') }}',
                                        text: response.msg
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('dashboard.error') }}',
                                    text: xhr.responseJSON?.msg || '{{ __('dashboard.something_went_wrong') }}'
                                });
                            }
                        });
                    }
                });
            });
        </script>
    @endpush

    <!-- Modals -->
    <x-dashboard.users.send-notification-modal :userId="$row->id" />
    <x-dashboard.users.charge-wallet-modal :userId="$row->id" :currentBalance="$row->wallet->available_balance ?? 0" />
</x-dashboard.layouts.master>
