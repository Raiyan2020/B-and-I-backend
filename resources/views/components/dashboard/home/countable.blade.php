@php
    $link = $link ?? null;
    $icon = $icon ?? 'feather icon-package';
@endphp
<div class="col-lg-4 col-md-6 col-12">
    @if($link)
        <a href="{{ $link }}" class="dashboard-card-link" style="text-decoration: none; color: inherit; display: block;">
    @endif
    <div class="card statistics-card" data-color="{{ $color }}" style="{{ $link ? 'cursor: pointer;' : '' }}">
        <div class="card-content">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted">{{ $slug }}</p>
                        <h2 class="text-bold-700 mt-1 mb-0">{{ number_format($count) }}</h2>
                    </div>
                    <div class="avatar bg-rgba-{{ $color }} p-50">
                        <div class="avatar-content">
                            <i class="{{ $icon }} text-{{ $color }} font-large-1"></i>
                        </div>
                    </div>
                </div>
                @if($link)
                    <div class="mt-2">
                        <small class="text-{{ $color }}">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                            {{ __('dashboard.view all') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if($link)
        </a>
    @endif
</div>

<style>
    .statistics-card {
        transition: all 0.3s ease;
        border-left: 4px solid #e0e0e0;
    }
    
    .statistics-card[data-color="primary"] {
        border-left-color: #9C88FF;
    }
    
    .statistics-card[data-color="success"] {
        border-left-color: #66BB6A;
    }
    
    .statistics-card[data-color="danger"] {
        border-left-color: #E57373;
    }
    
    .statistics-card[data-color="info"] {
        border-left-color: #64B5F6;
    }
    
    .statistics-card[data-color="warning"] {
        border-left-color: #FFB74D;
    }
    
    .dashboard-card-link:hover .statistics-card {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    
    .statistics-card h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #5e5873;
    }

    /* Dark Mode Improvements */
    body.dark-layout .statistics-card {
        background: #2b3553 !important;
    }

    body.dark-layout .statistics-card h2 {
        color: #ebeefd !important;
    }

    body.dark-layout .statistics-card p {
        color: #b4b7bd !important;
    }

    body.dark-layout .statistics-card small {
        color: #c2c6dc !important;
    }
</style>
