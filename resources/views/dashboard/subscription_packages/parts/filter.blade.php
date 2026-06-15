<x-dashboard.collapsible-panel
    :title="__('dashboard.filter')"
    icon="icon-filter"
    panel-id="subscription-packages-filter-panel">
    <x-slot name="headerActions">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filter-btn" title="{{ __('dashboard.reset') }}">
            <i class="feather icon-refresh-cw" style="font-size: 14px;"></i>
        </button>
    </x-slot>
    <form id="filter-form" class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __('dashboard.name') }}</label>
                <input type="text" id="search-text" class="form-control"
                    placeholder="{{ __('dashboard.name') }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>{{ __('dashboard.table status') }}</label>
                <select id="status-filter" class="form-control">
                    <option value="">{{ __('dashboard.all') }}</option>
                    <option value="1">{{ __('dashboard.active') }}</option>
                    <option value="0">{{ __('dashboard.in-active') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-12 col-lg-12 d-flex align-items-end gap-2 mt-2">
            <button type="button" id="filter-btn" class="btn btn-primary">
                <i class="feather icon-filter"></i>
                {{ __('dashboard.filter') }}
            </button>
        </div>
    </form>
</x-dashboard.collapsible-panel>
