<div class="col-lg-8 col-md-12 col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="feather icon-trending-up text-primary"></i>
                {{ __('dashboard.trends') }}
            </h4>
            <div class="chart-legend">
                <span class="legend-item">
                    <span class="legend-color bg-primary"></span>
                    {{ __('dashboard.users') }}
                </span>
                <span class="legend-item">
                    <span class="legend-color bg-danger"></span>
                    {{ __('dashboard.admins list') }}
                </span>
                <span class="legend-item">
                    <span class="legend-color bg-success"></span>
                    {{ __('dashboard.categories') }}
                </span>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div id="trend-chart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        var trendChartOptions = {
            series: [{
                name: '{{ __('dashboard.users') }}',
                data: @json($clientsData)
            }, {
                name: '{{ __('dashboard.admins list') }}',
                data: @json($adminsData)
            }, {
                name: '{{ __('dashboard.categories') }}',
                data: @json($categoriesData)
            }],
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                zoom: {
                    enabled: true
                }
            },
            colors: ['#9C88FF', '#E57373', '#66BB6A'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            },
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            },
            xaxis: {
                categories: @json($last7Days),
                labels: {
                    style: {
                        colors: '#5e5873',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#5e5873',
                        fontSize: '12px'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'light'
            }
        };

        var trendChart = new ApexCharts(document.querySelector("#trend-chart"), trendChartOptions);
        trendChart.render();
    });
</script>
@endpush

@push('styles')
<style>
    .chart-legend {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #5e5873;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
    }

    @media (max-width: 768px) {
        .chart-legend {
            margin-top: 1rem;
            width: 100%;
        }
    }

    /* Dark Mode Improvements */
    body.dark-layout .chart-legend .legend-item {
        color: #c2c6dc !important;
    }

    body.dark-layout .card-header h4 {
        color: #ebeefd !important;
    }
</style>
@endpush
