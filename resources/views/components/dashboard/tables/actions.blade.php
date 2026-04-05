{{-- Table Actions Component - Reusable action buttons for tables --}}
@props([
    'id',
    'editRoute' => null,
    'deleteRoute' => null,
    'showRoute' => null,
    'statusRoute' => null,
    'customActions' => [],
    'permissions' => ['edit', 'delete'],
    'hideIf' => null, // Condition to hide actions (e.g., id == 1)
])

@php
    // Hide if condition is true
    if ($hideIf && (is_callable($hideIf) ? $hideIf() : $hideIf)) {
        return;
    }
@endphp

<div class="d-flex align-items-center gap-2">
    @if($editRoute && (empty($permissions) || in_array('edit', $permissions)))
        @canIf(!empty($permissions) && in_array('edit', $permissions), 'edit')
            <a class="btn btn-sm btn-icon btn-outline-primary" href="{{ $editRoute }}" title="{{ __('dashboard.edit') }}">
                <i class="feather icon-edit text-primary"></i>
            </a>
        @endcanIf
        @if(empty($permissions))
            <a class="btn btn-sm btn-icon btn-outline-primary" href="{{ $editRoute }}" title="{{ __('dashboard.edit') }}">
                <i class="feather icon-edit text-primary"></i>
            </a>
        @endif
    @endif

    @if($showRoute && (empty($permissions) || in_array('show', $permissions)))
        <a class="btn btn-sm btn-icon btn-outline-info" href="{{ $showRoute }}" title="{{ __('dashboard.show') }}">
            <i class="feather icon-eye text-info"></i>
        </a>
    @endif

    @if($statusRoute && (empty($permissions) || in_array('status', $permissions)))
        <a class="btn btn-sm btn-icon btn-outline-warning" href="{{ $statusRoute }}" title="{{ __('dashboard.change status') }}">
            <i class="feather icon-slash text-warning"></i>
        </a>
    @endif

    @if($deleteRoute && (empty($permissions) || in_array('delete', $permissions)))
        @canIf(!empty($permissions) && in_array('delete', $permissions), 'delete')
            <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-row" data-url="{{ $deleteRoute }}" title="{{ __('dashboard.delete') }}">
                <i class="feather icon-trash-2 text-danger"></i>
            </button>
        @endcanIf
        @if(empty($permissions))
            <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-row" data-url="{{ $deleteRoute }}" title="{{ __('dashboard.delete') }}">
                <i class="feather icon-trash-2 text-danger"></i>
            </button>
        @endif
    @endif

    {{-- Custom Actions --}}
    @foreach($customActions as $action)
        <a class="btn btn-sm btn-icon btn-outline-{{ $action['color'] ?? 'secondary' }}" 
           href="{{ $action['route'] ?? '#' }}" 
           title="{{ $action['title'] ?? '' }}">
            <i class="feather icon-{{ $action['icon'] ?? 'more-vertical' }} text-{{ $action['color'] ?? 'secondary' }}"></i>
        </a>
    @endforeach
</div>
