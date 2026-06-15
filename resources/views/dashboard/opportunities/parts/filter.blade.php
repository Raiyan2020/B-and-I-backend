<x-dashboard.collapsible-panel :title="__('dashboard.filter')" icon="icon-filter" panel-id="opportunities-filter-panel">
    <x-slot name="headerActions">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filter-btn" title="{{ __('dashboard.reset') }}">
            <i class="feather icon-refresh-cw" style="font-size: 14px;"></i>
        </button>
    </x-slot>
    <form id="filter-form" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">{{ __('dashboard.table status') }}</label>
            <select class="form-control" id="status-filter">
                <option value="">{{ __('dashboard.all') }}</option>
                <option value="pending">{{ __('dashboard.opportunity_status_pending') }}</option>
                <option value="needs_revision">{{ __('dashboard.opportunity_status_needs_revision') }}</option>
                <option value="published">{{ __('dashboard.opportunity_status_published') }}</option>
                <option value="reserved">{{ __('dashboard.opportunity_status_reserved') }}</option>
                <option value="completed">{{ __('dashboard.opportunity_status_completed') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('dashboard.goal') }}</label>
            <select class="form-control" id="goal-filter">
                <option value="">{{ __('dashboard.all') }}</option>
                <option value="sell_business">{{ __('dashboard.goal_sell_business') }}</option>
                <option value="request_investment">{{ __('dashboard.goal_request_investment') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('dashboard.search') }}</label>
            <input type="text" class="form-control" id="search-company" placeholder="{{ __('dashboard.company_name') }}">
        </div>
        <div class="col-md-12 d-flex align-items-end gap-2 mt-2">
            <button type="button" class="btn btn-primary" id="filter-btn">
                <i class="feather icon-filter"></i>
                {{ __('dashboard.filter') }}
            </button>
        </div>
    </form>
</x-dashboard.collapsible-panel>
