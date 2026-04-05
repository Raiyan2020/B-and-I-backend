{{-- Bulk Actions Bar - Appears when rows are selected --}}
<div class="bulk-actions-bar" style="display: none; position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%); z-index: 1000; background: #fff; padding: 1rem 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); border: 1px solid #e7e7e7;">
    <div class="d-flex align-items-center gap-3">
        <span class="font-weight-bold text-primary">
            <i class="feather icon-check-circle mr-1"></i>
            <span class="selected-count">0</span> {{ __('dashboard.selected items') }}
        </span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-danger bulk-delete-btn">
                <i class="feather icon-trash-2 mr-1"></i>
                {{ __('dashboard.delete selected') }}
            </button>
            <button type="button" class="btn btn-sm btn-secondary bulk-clear-selection">
                <i class="feather icon-x mr-1"></i>
                {{ __('dashboard.clear selection') }}
            </button>
        </div>
    </div>
</div>
