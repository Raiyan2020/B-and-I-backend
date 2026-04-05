@section('script')
    <script>
        var $primary = '#7367F0';

        var usersGainedChart = {
            chart: {
                height: 100,
                type: 'area',
                toolbar:{
                    show: false,
                },
                sparkline: {
                    enabled: true
                },
                grid: {
                    show: false,
                    padding: {
                        left: 0,
                        right: 0
                    }
                },
            },
            colors: [$primary],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 0.9,
                    opacityFrom: 0.7,
                    opacityTo: 0.5,
                    stops: [0, 80, 100]
                }
            },
            series: [{
                name: '{{__('dashboard.users')}}',
                data: [
                    {{$usersCountLatestFourMonth['LastMonth']}},
                    {{$usersCountLatestFourMonth['LatestTowMonth']}},
                    {{$usersCountLatestFourMonth['LatestThreeMonth']}},
                    {{$usersCountLatestFourMonth['LatestFourMonth']}}
                ]
            }],

            xaxis: {
                labels: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                }
            },
            yaxis: [{
                y: 0,
                offsetX: 0,
                offsetY: 0,
                padding: { left: 0, right: 0 },
            }],
            tooltip: {
                x: { show: false }
            },
        }
        var usersGained = new ApexCharts(
            document.querySelector("#users-gain-chart"),
            usersGainedChart
        );
        usersGained.render();

        ////////////////////////////////
        var $warning = '#FF9F43';

        var ordersOptions = {
            chart: {
                height: 100,
                type: 'area',
                toolbar:{
                    show: false,
                },
                sparkline: {
                    enabled: true
                },
                grid: {
                    show: false,
                    padding: {
                        left: 0,
                        right: 0
                    }
                },
            },
            colors: [$warning],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 0.9,
                    opacityFrom: 0.7,
                    opacityTo: 0.5,
                    stops: [0, 80, 100]
                }
            },
            series: [{
                name: '{{__('dashboard.orders')}}',
                data: [
                    {{$ordersCountLatestSevenDays['lastDay']}},
                    {{$ordersCountLatestSevenDays['latestTwoDay']}},
                    {{$ordersCountLatestSevenDays['latestThreeDay']}},
                    {{$ordersCountLatestSevenDays['latestFourDay']}},
                    {{$ordersCountLatestSevenDays['latestFiveDay']}},
                    {{$ordersCountLatestSevenDays['latestSexDay']}},
                    {{$ordersCountLatestSevenDays['lastSevenDay']}},
                ]
            }],

            xaxis: {
                labels: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                }
            },
            yaxis: [{
                y: 0,
                offsetX: 0,
                offsetY: 0,
                padding: { left: 0, right: 0 },
            }],
            tooltip: {
                x: { show: false }
            },
        }
        var ordersChart = new ApexCharts(
            document.querySelector("#ordersChart"),
            ordersOptions
        );
        ordersChart.render();

        ////////////////////////////////
        var $primary = '#7367F0';
        var $danger = '#EA5455';
        var $warning = '#FF9F43';
        // Radial Bar Chart
        // -----------------------------
        var orderDetailOptions = {
            chart: {
                height: 350,
                type: 'radialBar',
            },
            colors: [$primary, $warning, $danger],
            plotOptions: {
                radialBar: {
                    size: 180,
                    hollow: {
                        size: '20%'
                    },
                    track: {
                        strokeWidth: '100%',
                        margin: 15,
                    },
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            // color: $label_color,
                            formatter: function (w) {
                                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                return {{array_sum($orderDetail)}}
                            }
                        }
                    }
                }
            },
            series: [{{($orderDetail['pending']*100)/100}}, {{($orderDetail['finished']*100)/100}}, {{($orderDetail['canceled']*100)/100}}],
            labels: ['Pending', 'Finished', 'Rejected'],
        }
        var orderDetail = new ApexCharts(
            document.querySelector("#order-status-chart"),
            orderDetailOptions
        );
        orderDetail.render();
    </script>
@endsection
