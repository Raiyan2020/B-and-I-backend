<x-dashboard.layouts.master title="{{ __('dashboard.about_us_items_list') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- about us items list start -->
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.about_us_items_list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    <x-dashboard.collapsible-panel
                        :title="__('dashboard.about_us_settings')"
                        icon="icon-settings"
                        panel-id="about-us-settings-panel">
                        <form id="settings-form" class="row">
                            @csrf
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.about_us_title_ar') }}</label>
                                    <input type="text" class="form-control" name="about_us_title_ar"
                                        value="{{ $about_us_title_ar }}"
                                        placeholder="{{ __('dashboard.about_us_title_ar') }}"
                                        dir="rtl">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.about_us_title_en') }}</label>
                                    <input type="text" class="form-control" name="about_us_title_en"
                                        value="{{ $about_us_title_en }}"
                                        placeholder="{{ __('dashboard.about_us_title_en') }}"
                                        dir="ltr">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.about_us_description_ar') }}</label>
                                    <textarea class="form-control" name="about_us_description_ar" rows="4"
                                        placeholder="{{ __('dashboard.about_us_description_ar') }}"
                                        dir="rtl">{{ $about_us_description_ar }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.about_us_description_en') }}</label>
                                    <textarea class="form-control" name="about_us_description_en" rows="4"
                                        placeholder="{{ __('dashboard.about_us_description_en') }}"
                                        dir="ltr">{{ $about_us_description_en }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary btn-sm" id="save-settings-btn">
                                    <i class="feather icon-save"></i>
                                    {{ __('dashboard.save') }}
                                </button>
                            </div>
                        </form>
                    </x-dashboard.collapsible-panel>

                    <!-- Filter Section -->
                    @include('dashboard.about_us_items.parts.filter')

                    <!-- Column selectors with Export Options and print table -->
                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">{{ __('dashboard.about_us_items_list') }}</h4>
                                        @can('add-about-us-item')
                                            <a href="{{ route('admin.about_us_items.create') }}">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="mr-1 feather icon-plus"></i>{{ __('dashboard.add_about_us_item') }}
                                                </button>
                                            </a>
                                        @endcan
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">

                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped " id="about-us-items-table">
                                                    <thead>
                                                        <tr class="text text-center">
                                                            <x-dashboard.tables.select-all-checkbox />
                                                            <th>{{ __('dashboard.table image') }}</th>
                                                            <th>{{ __('dashboard.title') }}</th>
                                                            <th>{{ __('dashboard.description') }}</th>
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
                <!-- about us items list ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
    @push('page-scripts')
        @include('dashboard.about_us_items.parts.script')
    @endpush

    @push('page-styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/categories-index.css') }}">
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
