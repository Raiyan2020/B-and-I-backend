<div class="row mb-2">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="feather icon-filter text-primary"></i>
                    {{ __('dashboard.filter') }}
                </h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filter-btn" title="{{ __('dashboard.reset') }}">
                    <i class="feather icon-refresh-cw" style="font-size: 14px;"></i>
                </button>
            </div>
            <div class="card-body">
                <form id="filter-form" class="row g-3">
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label">{{ __('dashboard.order') }}</label>
                        <select class="form-control" id="order-filter">
                            <option value="DESC">{{ __('dashboard.desc') }}</option>
                            <option value="ASC" selected>{{ __('dashboard.asc') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label">{{ __('dashboard.block_status') }}</label>
                        <select class="form-control" id="block-status-filter">
                            <option value="">{{ __('dashboard.all') }}</option>
                            <option value="1">{{ __('dashboard.blocked') }}</option>
                            <option value="0">{{ __('dashboard.un_blocked') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label">{{ __('dashboard.phone_activation_status') }}</label>
                        <select class="form-control" id="active-status-filter">
                            <option value="">{{ __('dashboard.all') }}</option>
                            <option value="1">{{ __('dashboard.activated') }}</option>
                            <option value="0">{{ __('dashboard.not_activated') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label">{{ __('dashboard.search by name') }}</label>
                        <input type="text" class="form-control" id="search-name"
                            placeholder="{{ __('dashboard.search') }}...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label">{{ __('dashboard.search by phone') }}</label>
                        <input type="text" class="form-control" id="search-phone"
                            placeholder="{{ __('dashboard.search') }}...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label">{{ __('dashboard.search by email') }}</label>
                        <input type="text" class="form-control" id="search-email"
                            placeholder="{{ __('dashboard.search') }}...">
                    </div>

                    <div class="col-md-12 col-lg-12 d-flex align-items-end gap-2 mt-2">
                        <button type="button" class="btn btn-primary" id="filter-btn">
                            <i class="feather icon-filter"></i>
                            {{ __('dashboard.filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
