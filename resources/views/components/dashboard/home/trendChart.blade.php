<div class="col-lg-8 col-md-12 col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="feather icon-trending-up text-primary"></i>
                {{ __('dashboard.category_performance_chart') }}
            </h4>
            <div class="chart-legend">
                <span class="legend-item">
                    <span class="legend-color bg-primary"></span>
                    {{ __('dashboard.opportunities_menu') }}
                </span>
                <span class="legend-item">
                    <span class="legend-color bg-warning"></span>
                    {{ __('dashboard.investment_seats_menu') }}
                </span>
                <span class="legend-item">
                    <span class="legend-color bg-success"></span>
                    {{ __('dashboard.interest_requests_menu') }}
                </span>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">
                <p class="text-muted mb-2">{{ __('dashboard.category_performance_chart_hint') }}</p>
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
                name: '{{ __('dashboard.opportunities_menu') }}',
                data: @json($categoryChart['adsData'] ?? [])
            }, {
                name: '{{ __('dashboard.investment_seats_menu') }}',
                data: @json($categoryChart['seatsData'] ?? [])
            }, {
                name: '{{ __('dashboard.interest_requests_menu') }}',
                data: @json($categoryChart['interestsData'] ?? [])
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                }
            },
            colors: ['#9C88FF', '#FFB74D', '#66BB6A'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 0
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 6,
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            },
            xaxis: {
                categories: @json($categoryChart['labels'] ?? []),
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
                theme: 'light',
                y: {
                    formatter: function (value) {
                        return value + ' {{ __('dashboard.count_unit') }}';
                    }
                }
            },
            noData: {
                text: '{{ __('dashboard.no_data_available_in_chart') }}'
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

    body.dark-layout .chart-legend .legend-item {
        color: #c2c6dc !important;
    }

    body.dark-layout .card-header h4 {
        color: #ebeefd !important;
    }
</style>
@endpush
