<x-dashboard.layouts.master title="{{ __('dashboard.preferred sectors list') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.preferred sectors list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    @include('dashboard.preferred_sectors.parts.filter')

                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                        <h4 class="card-title mb-0">{{ __('dashboard.preferred sectors list') }}</h4>
                                        <div class="card-header-actions">
                                            <x-dashboard.tables.bulk-actions-bar />
                                            @can('add-preferred-sector')
                                                <a href="{{ route('admin.preferred_sectors.create') }}">
                                                    <button class="btn btn-primary btn-sm" type="button">
                                                        <i class="mr-1 feather icon-plus"></i>{{ __('dashboard.add preferred sector') }}
                                                    </button>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped" id="preferred-sectors-table">
                                                    <thead>
                                                        <tr class="text text-center">
                                                            <x-dashboard.tables.select-all-checkbox />
                                                            <th>{{ __('dashboard.table name') }}</th>
                                                            <th>{{ __('dashboard.table status') }}</th>
                                                            <th>{{ __('dashboard.table create date') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text text-center">
                                                    </tbody>
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
        @include('dashboard.preferred_sectors.parts.script')
    @endpush

    @push('vendor-styles')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    @endpush

    @push('vendor-scripts')
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}">
        </script>
    @endpush

</x-dashboard.layouts.master>
