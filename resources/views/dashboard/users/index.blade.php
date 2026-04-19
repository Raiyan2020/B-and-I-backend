<x-dashboard.layouts.master title="{{ $listTitle ?? __('dashboard.users list') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users list start -->
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ $listTitle ?? __('dashboard.users list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    <!-- Filter Section -->
                    @include('dashboard.users.parts.filter')

                    <!-- Column selectors with Export Options and print table -->
                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">{{ $listTitle ?? __('dashboard.users list') }}</h4>
                                        @can('add-user')
                                            <a href="{{ route($createRouteName ?? 'admin.users.create') }}">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="mr-1 feather icon-plus"></i>{{ $createTitle ?? __('dashboard.add user') }}
                                                </button>
                                            </a>
                                        @endcan
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">

                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped " id="users-table">
                                                    <thead>
                                                        <tr class="text text-center">
                                                            <x-dashboard.tables.select-all-checkbox />
                                                            <th>{{ __('dashboard.table image') }}</th>
                                                            <th>{{ __('dashboard.table name') }}</th>
                                                            <th>{{ __('dashboard.full_phone') }}</th>
                                                            <th>{{ __('dashboard.table email') }}</th>
                                                            <th>{{ __('dashboard.table status') }}</th>
                                                            <th>{{ __('dashboard.account_status') }}</th>
                                                            <th>{{ __('dashboard.profile_update_request') }}</th>
                                                            <th>{{ __('dashboard.account_deletion_request') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text text-center ">
                                                    </tbody>
                                                </table>
                                                <x-dashboard.tables.bulk-actions-bar />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Column selectors with Export Options and print table -->
                </section>
                <!-- users list ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
    @push('page-scripts')
        @include('dashboard.users.parts.script')
    @endpush

    @push('page-styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/users-index.css') }}">
    @endpush

    @push('vendor-styles')
        <!-- DataTables CSS (Page-specific)-->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    @endpush

    @push('vendor-scripts')
        <!-- DataTables JS (Page-specific)-->
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}">
        </script>
    @endpush

</x-dashboard.layouts.master>
