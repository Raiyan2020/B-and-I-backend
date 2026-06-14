<x-dashboard.collapsible-panel
    :title="__('dashboard.filter')"
    icon="icon-filter"
    panel-id="users-filter-panel">
    <x-slot name="headerActions">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filter-btn" title="{{ __('dashboard.reset') }}">
            <i class="feather icon-refresh-cw" style="font-size: 14px;"></i>
        </button>
    </x-slot>
    <form id="filter-form" class="row g-3">
        <div class="col-md-3 col-lg-2">
            <label class="form-label">{{ __('dashboard.order') }}</label>
            <select class="form-control" id="order-filter">
                <option value="DESC" selected>{{ __('dashboard.desc') }}</option>
                <option value="ASC">{{ __('dashboard.asc') }}</option>
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
            <label class="form-label">{{ __('dashboard.account_status') }}</label>
            <select class="form-control" id="account-status-filter">
                <option value="">{{ __('dashboard.all') }}</option>
                <option value="1">{{ __('dashboard.active') }}</option>
                <option value="0">{{ __('dashboard.inactive') }}</option>
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label">{{ __('dashboard.search by name') }}</label>
            <input type="text" class="form-control" id="search-name"
                placeholder="{{ __('dashboard.search') }}...">
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label">{{ __('dashboard.search by full phone') }}</label>
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
</x-dashboard.collapsible-panel>
