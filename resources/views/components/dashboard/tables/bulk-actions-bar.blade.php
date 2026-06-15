{{-- Bulk Actions Bar - Shown in card header when rows are selected --}}
<div class="bulk-actions-bar" aria-live="polite">
    <span class="font-weight-bold text-primary mb-0">
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
