<x-dashboard.layouts.master title="{{ __('dashboard.category details') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.category details') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.categories.index') }}">{{ __('dashboard.categories list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="content-body">
                <!-- page category view start -->
                <section class="page-users-view">
                    <div class="row">
                        <!-- Category Info Card -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">{{ __('dashboard.category details') }}</h4>
                                    <div class="d-flex gap-2">
                                        @can('edit-category')
                                            <a href="{{ route('admin.categories.edit', $row->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather icon-edit mr-1"></i>{{ __('dashboard.edit') }}
                                            </a>
                                        @endcan
                                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="feather icon-arrow-right mr-1"></i>{{ __('dashboard.back') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Category Image -->
                                        <div class="col-12 col-md-4 text-center mb-3">
                                            <div class="category-view-image">
                                                @if($row->image)
                                                    <img src="{{ $row->image }}"
                                                        class="category-avatar-shadow w-100 rounded mb-2"
                                                        alt="Category Image"
                                                        style="max-width: 200px; height: 200px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div class="category-avatar-placeholder rounded d-inline-flex align-items-center justify-content-center mb-2"
                                                        style="width: 200px; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 auto; border-radius: 8px;">
                                                        <i class="feather icon-image text-white" style="font-size: 80px;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Category Details -->
                                        <div class="col-12 col-md-8">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-list text-primary mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table name') }} ({{ __('dashboard.in arabic') }})</p>
                                                            <h5 class="mb-0">{{ $row->getTranslation('name', 'ar') }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-list text-info mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table name') }} ({{ __('dashboard.in english') }})</p>
                                                            <h5 class="mb-0">{{ $row->getTranslation('name', 'en') }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-slash text-warning mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table status') }}</p>
                                                            <h5 class="mb-0">
                                                                @if($row->status)
                                                                    <span class="badge badge-success">{{ __('dashboard.active') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('dashboard.in-active') }}</span>
                                                                @endif
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-list text-success mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.order') }}</p>
                                                            <h5 class="mb-0">{{ $row->order ?? __('dashboard.not specified') }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-calendar text-secondary mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table create date') }}</p>
                                                            <h5 class="mb-0">{{ $row->created_at_formatted ?? $row->created_at }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- page category view ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
</x-dashboard.layouts.master>
