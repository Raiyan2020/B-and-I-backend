@php
    use App\Models\User;
    use App\Models\GeneralSetting;
@endphp
@section('styles')
    <style>
        .main-menu {
            display: none;
        }
    </style>
@endsection
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="{{ route('admin.home') }}">
                    <div class="">
                        <img style="width: 40px;"
                             src="{{ asset('Site/assets/images/logo/' . GeneralSetting::getValueForKey('logo1')) }}">
                    </div>
                    <h2 class="brand-text mb-0" style="font-size: 1.20rem">{{ $site_name[app()->getLocale()] }}</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                        class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                        data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">

        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item {{ Route::is('admin.home') ? 'active' : '' }}">
                <a href="{{ route('admin.home') }}"><i class="feather icon-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">{{ __('dashboard.dashboard') }}</span></a>
            </li>

            <li class=" navigation-header"><span></span></li>

            <li class="{{ Route::is('admin.admins.*') ? 'active' : '' }} nav-item">
                <a href="{{ route('admin.admins.index') }}"><i class="feather icon-shield"></i>
                    <span class="menu-title" data-i18n="Admins">{{ __('dashboard.admins') }}</span></a>
            </li>

            <li class="{{ Route::is('admin.roles.*') ? 'active' : '' }} nav-item">
                <a href="{{ route('admin.roles.index') }}"><i class="feather icon-lock"></i>
                    <span class="menu-title" data-i18n="Roles">{{ __('dashboard.roles list') }}</span></a>
            </li>

            <li class="{{ Route::is('admin.advertisers.*') ? 'active' : '' }} nav-item">
                <a href="{{ route('admin.advertisers.index') }}"><i class="feather icon-briefcase"></i>
                    <span class="menu-title" data-i18n="Advertisers">{{ __('dashboard.advertisers_companies') }}</span></a>
            </li>

            <li class="{{ Route::is('admin.investors.*') ? 'active' : '' }} nav-item">
                <a href="{{ route('admin.investors.index') }}"><i class="feather icon-trending-up"></i>
                    <span class="menu-title" data-i18n="Investors">{{ __('dashboard.investors') }}</span></a>
            </li>
            {{--            start new side bar--}}


            <li class=" nav-item {{ Route::is('admin.categories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}">
                    <i class="feather icon-list"></i>
                    <span class="menu-title" data-i18n="Wish List">{{ __('dashboard.categories') }}</span></a>
            </li>

            {{-- @can('preferred-sectors') --}}
                <li class=" nav-item {{ Route::is('admin.preferred_sectors.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.preferred_sectors.index') }}">
                        <i class="feather icon-target"></i>
                        <span class="menu-title">{{ __('dashboard.preferred sectors') }}</span></a>
                </li>
            {{-- @endcan --}}

            {{-- @can('about-us-items') --}}
                <li class=" nav-item {{ Route::is('admin.about_us_items.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.about_us_items.index') }}">
                        <i class="feather icon-info"></i>
                        <span class="menu-title">{{ __('dashboard.about_us_management') }}</span></a>
                </li>
            {{-- @endcan --}}

            {{-- @can('features') --}}
                <li class=" nav-item {{ Route::is('admin.features.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.features.index') }}">
                        <i class="feather icon-star"></i>
                        <span class="menu-title">{{ __('dashboard.features') }}</span></a>
                </li>
            {{-- @endcan --}}

            <li class=" nav-item {{ Route::is('admin.subscription_packages.*') ? 'active' : '' }}">
                <a href="{{ route('admin.subscription_packages.index') }}">
                    <i class="feather icon-layers"></i>
                    <span class="menu-title">{{ __('dashboard.subscription_packages_menu') }}</span></a>
            </li>

            <li class=" nav-item {{ Route::is('admin.opportunities.*') ? 'active' : '' }}">
                <a href="{{ route('admin.opportunities.index') }}">
                    <i class="feather icon-briefcase"></i>
                    <span class="menu-title">{{ __('dashboard.opportunities_menu') }}</span></a>
            </li>

            <li class=" nav-item {{ Route::is('admin.investment-seats.*') ? 'active' : '' }}">
                <a href="{{ route('admin.investment-seats.index') }}">
                    <i class="feather icon-file-text"></i>
                    <span class="menu-title">{{ __('dashboard.investment_seats_menu') }}</span></a>
            </li>

            <li class=" nav-item {{ Route::is('admin.interest-requests.*') ? 'active' : '' }}">
                <a href="{{ route('admin.interest-requests.index') }}">
                    <i class="feather icon-mail"></i>
                    <span class="menu-title">{{ __('dashboard.interest_requests_menu') }}</span></a>
            </li>

            <li class=" nav-item {{ Route::is('admin.company-investor-interest-requests.*') ? 'active' : '' }}">
                <a href="{{ route('admin.company-investor-interest-requests.index') }}">
                    <i class="feather icon-user-check"></i>
                    <span class="menu-title">{{ __('dashboard.company_investor_interest_requests_menu') }}</span></a>
            </li>

            <li class="{{ Route::is('admin.generalSetting.index') ? 'active' : '' }} nav-item">
                <a href="{{ route('admin.generalSetting.index') }}"><i class="feather icon-settings"></i>
                    <span class="menu-title" data-i18n="Ecommerce">{{ __('dashboard.general settings') }}</span></a>
            </li>

        </ul>
    </div>
</div>
