<x-dashboard.layouts.master title="{{ __('dashboard.features list') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- features list start -->
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.features list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    <!-- Filter Section -->
                    @include('dashboard.features.parts.filter')

                    <!-- Column selectors with Export Options and print table -->
                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">{{ __('dashboard.features list') }}</h4>
                                        @can('add-feature')
                                            <a href="{{ route('admin.features.create') }}">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="mr-1 feather icon-plus"></i>{{ __('dashboard.add feature') }}
                                                </button>
                                            </a>
                                        @endcan
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">

                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped " id="features-table">
                                                    <thead>
                                                        <tr class="text text-center">
                                                            <x-dashboard.tables.select-all-checkbox />
                                                            <th>{{ __('dashboard.table image') }}</th>
                                                            <th>{{ __('dashboard.title in arabic') }}</th>
                                                            <th>{{ __('dashboard.title in english') }}</th>
                                                            <th>{{ __('dashboard.table status') }}</th>
                                                            <th>{{ __('dashboard.table create date') }}</th>
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
                <!-- features list ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
    @push('page-scripts')
        @include('dashboard.features.parts.script')
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
