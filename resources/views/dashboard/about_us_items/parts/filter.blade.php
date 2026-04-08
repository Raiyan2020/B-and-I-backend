<x-dashboard.collapsible-panel
    :title="__('dashboard.filter')"
    icon="icon-filter"
    panel-id="about-us-filter-panel">
    <x-slot name="headerActions">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filter-btn" title="{{ __('dashboard.reset') }}">
            <i class="feather icon-refresh-cw" style="font-size: 14px;"></i>
        </button>
    </x-slot>
    <form id="filter-form" class="row g-3">
        <div class="col-md-3 col-lg-2">
            <label class="form-label">{{ __('dashboard.table status') }}</label>
            <select class="form-control" id="status-filter">
                <option value="">{{ __('dashboard.all') }}</option>
                <option value="1">{{ __('dashboard.active') }}</option>
                <option value="0">{{ __('dashboard.in-active') }}</option>
            </select>
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">{{ __('dashboard.search_title_or_description') }}</label>
            <input type="text" class="form-control" id="search-text"
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
