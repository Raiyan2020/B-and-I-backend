@php
    use Illuminate\Support\Facades\App;
@endphp

<x-dashboard.layouts.master title="{{ __('dashboard.dashboard') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">{{ __('dashboard.dashboard') }}</h3>
                </div>
            </div>

            <div class="content-body">
                <section id="dashboard-analytics">

                    <!-- Welcome Banner -->
                    <div class="row match-height mb-1">
                        <x-dashboard.home.welcome />
                    </div>

                    <!-- Divider: Statistics -->
                    <div class="row">
                        <div class="col-12">
                            <div class="section-divider">
                                <span class="divider-line"></span>
                                <span class="divider-chip">
                                    <i class="feather icon-bar-chart-2"></i>
                                    {{ __('dashboard.statistics') }}
                                </span>
                                <span class="divider-line"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Statistics Cards with Charts -->
                    <div class="row match-height">
                        @foreach($statCards as $statCard)
                            <x-dashboard.home.statCardWithChart
                                :count="$statCard['count']"
                                :chartData="$statCard['chartData']"
                                :growth="$statCard['growth']"
                                :todayCount="$statCard['todayCount']"
                                :color="$statCard['color']"
                                :slug="$statCard['title']"
                                :link="$statCard['link']"
                                :icon="$statCard['icon']" />
                        @endforeach
                    </div>

                    <!-- Divider: Trends & Analytics -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="section-divider">
                                <span class="divider-line"></span>
                                <span class="divider-chip">
                                    <i class="feather icon-trending-up"></i>
                                    {{ __('dashboard.trends and analytics') }}
                                </span>
                                <span class="divider-line"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Trends Chart and Recent Activity -->
                    <div class="row match-height">
                        <x-dashboard.home.trendChart
                            :categoryChart="$categoryChart" />

                        <x-dashboard.home.recentActivity
                            :activitySections="$activitySections" />
                    </div>

                    <!-- Divider: Quick Links -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="section-divider">
                                <span class="divider-line"></span>
                                <span class="divider-chip">
                                    <i class="feather icon-zap"></i>
                                    {{ __('dashboard.quick links') }}
                                </span>
                                <span class="divider-line"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links Section -->
                    <div class="row">
                        <x-dashboard.home.quickLinks />
                    </div>

                </section>
            </div>
        </div>
    </div>

    @push('vendor-styles')
    <!-- ApexCharts CSS (Page-specific - Used for dashboard charts)-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('dashboardAssets/app-assets/vendors/css/charts/apexcharts.css') }}">
    @endpush

    @push('page-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/dashboard-home.css') }}">
    @endpush

    @push('vendor-scripts')
    <!-- ApexCharts JS (Page-specific - Used for dashboard charts)-->
    <script src="{{asset('dashboardAssets/app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
    @endpush

    @push('page-scripts')
    <script src="{{ asset('dashboardAssets/custom/js/dashboard-home.js') }}"></script>
    @endpush
</x-dashboard.layouts.master>
