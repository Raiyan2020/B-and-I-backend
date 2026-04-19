<x-dashboard.layouts.master title="{{ __('dashboard.subscription_packages_list') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.subscription_packages_list') }}">
                    </x-dashboard.layouts.breadcrumb>

                    <x-dashboard.collapsible-panel :title="__('dashboard.packages_page_settings')" icon="icon-settings"
                        panel-id="subscription-packages-settings-panel">
                        <form id="settings-form" class="row">
                            @csrf
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.packages_page_title_ar') }}</label>
                                    <input type="text" class="form-control" name="packages_page_title_ar"
                                        value="{{ $packages_page_title_ar }}" dir="rtl">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.packages_page_title_en') }}</label>
                                    <input type="text" class="form-control" name="packages_page_title_en"
                                        value="{{ $packages_page_title_en }}" dir="ltr">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.packages_page_description_ar') }}</label>
                                    <textarea id="packages_page_description_ar" class="form-control ckeditor-packages-settings"
                                        name="packages_page_description_ar" rows="6" dir="rtl">{{ $packages_page_description_ar }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('dashboard.packages_page_description_en') }}</label>
                                    <textarea id="packages_page_description_en" class="form-control ckeditor-packages-settings"
                                        name="packages_page_description_en" rows="6" dir="ltr">{{ $packages_page_description_en }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-primary btn-sm" id="save-settings-btn">
                                    <i class="feather icon-save"></i> {{ __('dashboard.save') }}
                                </button>
                            </div>
                        </form>
                    </x-dashboard.collapsible-panel>

                    @include('dashboard.subscription_packages.parts.filter')



                    <section id="column-selectors">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">{{ __('dashboard.subscription_packages_list') }}
                                        </h4>
                                        @can('add-subscription-package')
                                            <div class="row mb-2">
                                                <div class="col-12 d-flex justify-content-end">
                                                    <a href="{{ route('admin.subscription_packages.create') }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i
                                                            class="mr-1 feather icon-plus"></i>{{ __('dashboard.add_subscription_package') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body card-dashboard">
                                            <div class="table-responsive overflow-auto">
                                                <table class="table table-striped" id="subscription-packages-table">
                                                    <thead>
                                                        <tr class="text text-center">
                                                            <x-dashboard.tables.select-all-checkbox />
                                                            <th>{{ __('dashboard.name') }}</th>
                                                            <th>{{ __('dashboard.price_monthly') }}</th>
                                                            <th>{{ __('dashboard.description') }}</th>
                                                            <th>{{ __('dashboard.table status') }}</th>
                                                            <th>{{ __('dashboard.table create date') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text text-center"></tbody>
                                                </table>
                                                <x-dashboard.tables.bulk-actions-bar />
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
        @include('dashboard.subscription_packages.parts.script')
    @endpush

    @push('page-styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/categories-index.css') }}">
    @endpush

    @push('vendor-styles')
        {{-- <link rel="stylesheet" href="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.css"> --}}
    @endpush

    @push('vendor-scripts')
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}">
        </script>
        {{-- <script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script> --}}
    @endpush
</x-dashboard.layouts.master>
