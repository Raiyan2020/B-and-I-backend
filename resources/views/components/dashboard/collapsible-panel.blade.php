@props([
    'title',
    'icon' => 'icon-filter',
    'collapsed' => true,
    'panelId' => null,
])

@php
    $targetId = $panelId ?? 'admin-cp-' . str_replace('.', '', uniqid('', true));
@endphp

<div class="row admin-collapsible-panel">
    <div class="col-12">
        <div class="card border admin-collapsible-card">
            <div
                class="card-header admin-collapsible-panel-toggle d-flex justify-content-between align-items-center flex-wrap user-select-none"
                data-toggle="collapse"
                data-target="#{{ $targetId }}"
                role="button"
                aria-expanded="{{ $collapsed ? 'false' : 'true' }}"
                aria-controls="{{ $targetId }}">
                <h5 class="card-title mb-0 d-flex align-items-center flex-grow-1">
                    <i class="feather {{ $icon }} text-primary mr-50"></i>
                    <span>{{ $title }}</span>
                    <i class="feather icon-chevron-down admin-collapsible-chevron ml-50 {{ $collapsed ? '' : 'is-open' }}"></i>
                </h5>
                @isset($headerActions)
                    <div class="admin-collapsible-header-actions align-self-center" onclick="event.stopPropagation();">
                        {{ $headerActions }}
                    </div>
                @endisset
            </div>
            <div id="{{ $targetId }}" class="collapse {{ $collapsed ? '' : 'show' }} admin-collapsible-panel-body">
                <div class="card-body border-top">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
