<x-dashboard.layouts.master title="{{ __('dashboard.interest_requests_list') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.interest_requests_list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">{{ __('dashboard.interest_requests_list') }}</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped" id="interest-requests-table">
                                                    <thead>
                                                        <tr class="text text-center">
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.advertiser') }}</th>
                                                            <th>{{ __('dashboard.investor') }}</th>
                                                            <th>{{ __('dashboard.seat_reference') }}</th>
                                                            <th>{{ __('dashboard.submitted_at') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text text-center"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </section>
            </div>
        </div>
    </div>

    @push('page-scripts')
        @include('dashboard.interest_requests.parts.script')
    @endpush

    @push('vendor-styles')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    @endpush

    @push('vendor-scripts')
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    @endpush
</x-dashboard.layouts.master>
