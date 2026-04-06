<x-dashboard.layouts.master title="{{ __('dashboard.preferred sector details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.preferred sector details') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.preferred_sectors.index') }}">{{ __('dashboard.preferred sectors list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="content-body">
                <section class="page-users-view">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">{{ __('dashboard.preferred sector details') }}</h4>
                                    <div class="d-flex gap-2">
                                        @can('edit-preferred-sector')
                                            <a href="{{ route('admin.preferred_sectors.edit', $row->id) }}" class="btn btn-sm btn-primary">
                                                <i class="feather icon-edit mr-1"></i>{{ __('dashboard.edit') }}
                                            </a>
                                        @endcan
                                        <a href="{{ route('admin.preferred_sectors.index') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="feather icon-arrow-right mr-1"></i>{{ __('dashboard.back') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
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
                </section>
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
