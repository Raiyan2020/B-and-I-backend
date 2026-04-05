@php
    $link = $link ?? null;
    $icon = $icon ?? 'feather icon-package';
    $chartData = $chartData ?? [];
    $growth = $growth ?? 0;
    $todayCount = $todayCount ?? 0;
@endphp

<div class="col-lg-4 col-md-6 col-12">
    @if($link)
        <a href="{{ $link }}" class="dashboard-card-link" style="text-decoration: none; color: inherit; display: block;">
    @endif
    <div class="card statistics-card-enhanced" data-color="{{ $color }}" style="{{ $link ? 'cursor: pointer;' : '' }}">
        <div class="card-content">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted small">{{ $slug }}</p>
                        <h2 class="text-bold-700 mt-1 mb-0">{{ number_format($count) }}</h2>
                        @if($todayCount > 0)
                            <p class="mb-0 mt-1">
                                <span class="badge badge-light-{{ $color }}">{{ __('dashboard.today') }}: {{ $todayCount }}</span>
                            </p>
                        @endif
                    </div>
                    <div class="avatar bg-rgba-{{ $color }} p-50">
                        <div class="avatar-content">
                            <i class="{{ $icon }} text-{{ $color }} font-large-1"></i>
                        </div>
                    </div>
                </div>

                @if(count($chartData) > 0)
                    <div class="mini-chart-container mt-2">
                        <div id="mini-chart-{{ $color }}" style="height: 60px;"></div>
                    </div>
                @endif

                @if($growth != 0)
                    <div class="growth-indicator mt-2">
                        <span class="growth-value text-{{ $growth > 0 ? 'success' : 'danger' }}">
                            <i class="feather icon-arrow-{{ $growth > 0 ? 'up' : 'down' }}"></i>
                            {{ abs($growth) }}%
                        </span>
                        <span class="growth-label text-muted small">{{ __('dashboard.vs last week') }}</span>
                    </div>
                @endif

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

@push('script')
<script>
    $(document).ready(function() {
        @if(count($chartData) > 0)
        var miniChart{{ ucfirst($color) }} = {
            series: [{
                name: '{{ $slug }}',
                data: @json($chartData)
            }],
            chart: {
                type: 'area',
                height: 60,
                sparkline: {
                    enabled: true
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['{{ $color === 'primary' ? '#9C88FF' : ($color === 'success' ? '#66BB6A' : '#E57373') }}'],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 0.9,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 80, 100]
                }
            },
            tooltip: {
                fixed: {
                    enabled: false
                },
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: function (seriesName) {
                            return ''
                        }
                    }
                },
                marker: {
                    show: false
                }
            }
        };

        var chart{{ ucfirst($color) }} = new ApexCharts(document.querySelector("#mini-chart-{{ $color }}"), miniChart{{ ucfirst($color) }});
        chart{{ ucfirst($color) }}.render();
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    .statistics-card-enhanced {
        transition: all 0.3s ease;
        border-left: 4px solid #e0e0e0;
        position: relative;
        overflow: hidden;
    }

    .statistics-card-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(156, 136, 255, 0.08) 0%, rgba(139, 126, 200, 0.08) 100%);
        border-radius: 50%;
        transform: translate(30%, -30%);
        pointer-events: none;
    }
    
    .statistics-card-enhanced[data-color="primary"] {
        border-left-color: #9C88FF;
    }
    
    .statistics-card-enhanced[data-color="success"] {
        border-left-color: #66BB6A;
    }
    
    .statistics-card-enhanced[data-color="danger"] {
        border-left-color: #E57373;
    }
    
    .statistics-card-enhanced[data-color="info"] {
        border-left-color: #00cfe8;
    }
    
    .statistics-card-enhanced[data-color="warning"] {
        border-left-color: #ff9f43;
    }
    
    .dashboard-card-link:hover .statistics-card-enhanced {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    
    .statistics-card-enhanced h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #5e5873;
    }

    .mini-chart-container {
        margin-top: 1rem;
        padding-top: 0.5rem;
        border-top: 1px solid #f3f3f3;
    }

    .growth-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .growth-value {
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .growth-label {
        font-size: 0.75rem;
    }

    /* Dark Mode Improvements */
    body.dark-layout .statistics-card-enhanced {
        background: #2b3553 !important;
        border-left-color: #8B7EC8 !important;
    }

    body.dark-layout .statistics-card-enhanced h2 {
        color: #ebeefd !important;
    }

    body.dark-layout .statistics-card-enhanced p {
        color: #b4b7bd !important;
    }

    body.dark-layout .statistics-card-enhanced small {
        color: #c2c6dc !important;
    }

    body.dark-layout .statistics-card-enhanced .growth-value {
        color: #66BB6A !important;
    }

    body.dark-layout .statistics-card-enhanced .growth-value.text-danger {
        color: #E57373 !important;
    }
</style>
@endpush
