<x-dashboard.layouts.master title="{{ __('dashboard.company_investor_interest_requests_list') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.company_investor_interest_requests_list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">{{ __('dashboard.filter') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 col-12 mb-1">
                                            <label>{{ __('dashboard.advertiser') }}</label>
                                            <input type="text" id="company-name-filter" class="form-control"
                                                   value="{{ $companyName }}"
                                                   placeholder="{{ __('dashboard.filter_by_company_name') }}">
                                            <input type="hidden" id="company-id-filter" value="{{ $companyId }}">
                                        </div>
                                        <div class="col-md-4 col-12 mb-1">
                                            <label>{{ __('dashboard.investor') }}</label>
                                            <input type="text" id="investor-name-filter" class="form-control"
                                                   value="{{ $investorName }}"
                                                   placeholder="{{ __('dashboard.filter_by_investor_name') }}">
                                        </div>
                                        <div class="col-md-4 col-12 mb-1">
                                            <label>{{ __('dashboard.interest_date') }}</label>
                                            <input type="date" id="interest-date-filter" class="form-control" value="{{ $interestDate }}">
                                        </div>
                                        <div class="col-12 d-flex gap-1 mt-1">
                                            <button type="button" id="filter-btn" class="btn btn-primary mr-1">{{ __('dashboard.filter') }}</button>
                                            <button type="button" id="reset-filter-btn" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">{{ __('dashboard.company_investor_interest_requests_list') }}</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped" id="company-investor-interest-requests-table">
                                                    <thead>
                                                    <tr class="text text-center">
                                                        <th>#</th>
                                                        <th>{{ __('dashboard.advertiser') }}</th>
                                                        <th>{{ __('dashboard.investor') }}</th>
                                                        <th>{{ __('dashboard.interest_date') }}</th>
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
        @include('dashboard.company_investor_interest_requests.parts.script')
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
