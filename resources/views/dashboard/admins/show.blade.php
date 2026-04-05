<x-dashboard.layouts.master title="{{ __('dashboard.admin details') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.admin details') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.admins.index') }}">{{ __('dashboard.admins list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="content-body">
                <!-- page admin view start -->
                <section class="page-users-view">
                    <div class="row">
                        <!-- Admin Info Card -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">{{ __('dashboard.admin details') }}</h4>
                                    <div class="d-flex gap-2">
                                        @can('edit-admin')
                                            <a href="{{ route('admin.admins.edit', $row->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather icon-edit mr-1"></i>{{ __('dashboard.edit') }}
                                            </a>
                                        @endcan
                                        <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="feather icon-arrow-right mr-1"></i>{{ __('dashboard.back') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Admin Image -->
                                        <div class="col-12 col-md-4 text-center mb-3">
                                            <div class="admin-view-image">
                                                @if($row->image)
                                                    <img src="{{ $row->image }}"
                                                        class="admin-avatar-shadow w-100 rounded mb-2"
                                                        alt="Admin Avatar"
                                                        style="max-width: 200px; height: 200px; object-fit: cover; border-radius: 50%;">
                                                @else
                                                    <div class="admin-avatar-placeholder rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                        style="width: 200px; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 auto;">
                                                        <i class="feather icon-shield text-white" style="font-size: 80px;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Admin Details -->
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
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-mail text-info mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table email') }}</p>
                                                            <h5 class="mb-0">{{ $row->email }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-phone text-success mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.table phone') }}</p>
                                                            <h5 class="mb-0">{{ $row->phone ?? __('dashboard.not specified') }}</h5>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather icon-shield text-warning mr-2" style="font-size: 18px;"></i>
                                                        <div>
                                                            <p class="mb-0 font-weight-bold text-muted">{{ __('dashboard.role name') }}</p>
                                                            <h5 class="mb-0">
                                                                @if($row->roles && $row->roles->count() > 0)
                                                                    <span class="badge badge-primary">{{ $row->roles->first()->name }}</span>
                                                                @else
                                                                    <span class="badge badge-secondary">{{ __('dashboard.no role') }}</span>
                                                                @endif
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
                    </div>
                </section>
                <!-- page admin view end -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
</x-dashboard.layouts.master>
