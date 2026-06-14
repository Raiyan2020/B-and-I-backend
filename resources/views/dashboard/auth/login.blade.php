<!DOCTYPE html>
<html class="loading" lang="{{ app()->getLocale() }}" @if(app()->getLocale()=='ar') data-textdirection="rtl" @else data-textdirection="ltr" @endif>
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>{{__('dashboard.login')}}</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboardAssets/app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('dashboardAssets/app-assets/images/logo/N-FAVICON.png')}}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/vendors/css/vendors-rtl.min.css') }}">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css-rtl/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/components.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css-rtl/themes/dark-layout.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css-rtl/themes/semi-dark-layout.css') }}">

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css-rtl/core/colors/palette-gradient.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css-rtl/pages/authentication.css') }}">
        <!-- END: Page CSS-->

        <!-- BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/custom-rtl.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/assets/css/style-rtl.css') }}">
        <!-- END: Custom CSS-->
    @else
        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/vendors/css/vendors.min.css') }}">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/components.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css/themes/dark-layout.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css/themes/semi-dark-layout.css') }}">

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css/core/colors/palette-gradient.css') }}">
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/css/pages/authentication.css') }}">
        <!-- END: Page CSS-->

        <!-- BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/assets/css/style.css') }}">
        <!-- END: Custom CSS-->
    @endif

    @php
        use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
    @endphp

    <style>
        .language-switcher-wrapper {
            z-index: 1050;
            position: absolute;
            top: 1rem;
            right: 1rem;
            left: auto;
            background: rgba(255, 255, 255, 0.08);
            padding: 0.25rem 0.4rem;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            align-items: center;
        }
        .dropdown-language .dropdown-menu {
            min-width: 150px;
        }
        .language-switcher-wrapper .btn {
            border-radius: 6px;
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .language-switcher-wrapper .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .dark-mode-toggle {
            transition: all 0.3s ease;
        }

        .dark-mode-toggle:hover {
            transform: scale(1.05);
        }

        .dark-mode-toggle.active {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }

        body.dark-mode {
            background-color: #1a1a2e !important;
        }

        body.dark-mode .card {
            background-color: #16213e !important;
            color: #eaeaea !important;
        }

        body.dark-mode .card-header {
            background-color: #0f3460 !important;
            border-bottom-color: #16213e !important;
        }

        body.dark-mode .form-control {
            background-color: #0f3460 !important;
            border-color: #16213e !important;
            color: #eaeaea !important;
        }

        body.dark-mode .form-control:focus {
            background-color: #0f3460 !important;
            border-color: #667eea !important;
            color: #eaeaea !important;
        }

        body.dark-mode .form-control::placeholder {
            color: #8b8b8b !important;
        }

        body.dark-mode label {
            color: #eaeaea !important;
        }

        body.dark-mode .btn-primary {
            background-color: #667eea !important;
            border-color: #667eea !important;
        }

        body.dark-mode .btn-primary:hover {
            background-color: #5568d3 !important;
            border-color: #5568d3 !important;
        }

        body.dark-mode .btn-outline-primary {
            border-color: #667eea !important;
            color: #667eea !important;
        }

        body.dark-mode .btn-outline-primary:hover {
            background-color: #667eea !important;
            color: white !important;
        }

        body.dark-mode .btn-outline-secondary {
            border-color: #6c757d !important;
            color: #6c757d !important;
        }

        body.dark-mode .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            color: white !important;
        }

        body.dark-mode .dropdown-menu {
            background-color: #16213e !important;
            border-color: #0f3460 !important;
        }

        body.dark-mode .dropdown-item {
            color: #eaeaea !important;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: #0f3460 !important;
            color: #667eea !important;
        }

        body.dark-mode .text-danger {
            color: #ff6b6b !important;
        }

        /* Login Card Animation */
        .card.bg-authentication {
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Image Animation */
        .col-lg-6 img {
            animation: fadeInLeft 1s ease-out 0.3s both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Form Card Animation */
        .card.rounded-0.mb-0.px-2 {
            animation: fadeInRight 1s ease-out 0.5s both;
            padding-top: 3.5rem;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Input Fields Animation */
        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .form-label-group {
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .form-label-group:nth-child(1) {
            animation-delay: 0.7s;
        }

        .form-label-group:nth-child(2) {
            animation-delay: 0.8s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Button Animation */
        .btn-primary {
            transition: all 0.3s ease;
            animation: pulse 2s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
        }

        /* Card Header Animation */
        .card-header {
            animation: slideDown 0.6s ease-out 0.6s both;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Welcome Text Animation */
        .px-2 {
            animation: fadeIn 0.8s ease-out 0.7s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Icon Animation */
        .form-control-position i {
            transition: all 0.3s ease;
        }

        .form-control:focus + .form-control-position i {
            transform: scale(1.2);
            color: #667eea;
        }

        /* Background Animation */
        body.bg-full-screen-image::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.1;
            animation: gradientShift 10s ease infinite;
            z-index: -1;
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        /* Language Switcher Animation */
        .language-switcher-wrapper {
            animation: bounceIn 0.8s ease-out 0.4s both;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Error Message Animation */
        .text-danger {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-10px);
            }
            75% {
                transform: translateX(10px);
            }
        }

        @media (max-width: 768px) {
            .card.bg-authentication {
                width: calc(100% - 2rem);
                margin: 1rem auto;
                border-radius: 20px;
            }

            .card.rounded-0.mb-0.px-2 {
                padding-top: 4.5rem;
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .language-switcher-wrapper {
                position: static !important;
                width: 100%;
                margin-bottom: 1rem;
                padding: 0.65rem 0.75rem;
                justify-content: center;
                background: rgba(255, 255, 255, 0.09);
                border: 1px solid rgba(0, 0, 0, 0.06);
                box-shadow: none;
                left: auto !important;
                right: auto !important;
                top: auto !important;
            }

            .language-switcher-wrapper .btn {
                font-size: 0.82rem;
                padding: 0.4rem 0.65rem;
            }

            .dropdown-language .dropdown-toggle {
                width: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body
    class="vertical-layout vertical-menu-modern 1-column  navbar-floating footer-static bg-full-screen-image  menu-collapsed blank-page blank-page"
    data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-8 col-11 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                                    <img src="{{ asset('dashboardAssets/app-assets/images/pages/login.png') }}"
                                        alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-2 position-relative">
                                        <!-- Language Switcher & Dark Mode Toggle -->
                                        <div class="language-switcher-wrapper d-flex gap-2" style="position: absolute; top: 1rem; {{ app()->getLocale() == 'ar' ? 'left: 1rem; right: auto;' : 'right: 1rem; left: auto;' }};">
                                            <!-- Dark Mode Toggle -->
                                            {{-- <button type="button" class="btn btn-sm btn-outline-secondary dark-mode-toggle" id="dark-mode-toggle" title="Toggle Dark Mode">
                                                <i class="feather icon-moon"></i>
                                            </button> --}}

                                            <!-- Language Switcher -->
                                            <div class="dropdown dropdown-language">
                                                <a class="dropdown-toggle btn btn-sm btn-outline-primary" id="dropdown-flag" href="#" data-toggle="dropdown"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="flag-icon {{ app()->getLocale() == 'ar' ? 'flag-icon-eg' : 'flag-icon-us' }}"></i>
                                                    <span class="selected-language">{{ app()->getLocale() == 'ar' ? 'AR' : 'EN' }}</span>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                                                    <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('en') }}"
                                                       data-language="en">
                                                        <i class="flag-icon flag-icon-us"></i> EN
                                                    </a>
                                                    <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('ar') }}"
                                                       data-language="ar">
                                                        <i class="flag-icon flag-icon-eg"></i> AR
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0">{{ __('auth.login') }}</h4>
                                            </div>
                                        </div>
                                        <p class="px-2">{{ __('auth.welcome') }}.</p>
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                                <form action="{{ route('admin.startSession') }}" method="POST">
                                                    @csrf
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="email" class="form-control" id="user-name"
                                                            name="email"
                                                            placeholder="{{ __('auth.email') }}" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-user"></i>
                                                        </div>
                                                        <label for="email">{{ __('auth.email') }}</label>
                                                        @error('email')
                                                            <span class="text text-danger font-size-xsmall">{{ $message }}</span>
                                                        @enderror

                                                    </fieldset>

                                                    <fieldset class="form-label-group position-relative has-icon-left">
                                                        <input type="password" class="form-control"
                                                            id="user-password"
                                                            name="password"
                                                            placeholder="{{ __('auth.password') }}" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-lock"></i>
                                                        </div>
                                                        <label for="user-password">{{ __('auth.password') }}</label>
                                                         @error('password')
                                                            <span class="text text-danger font-size-xsmall">{{ $message }}</span>
                                                        @enderror
                                                    </fieldset>
                                                    @if(session('error'))
                                                    <span class="text text-danger font-size-xsmall">{{ session('error') }}</span>
                                                    @endif
                                                    <div
                                                        class="form-group d-flex justify-content-between align-items-center">
                                                    </div>
                                                    <button type="submit"
                                                        class="btn btn-primary btn-inline mb-2 form-control"
                                                         >{{ __('auth.login') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('dashboardAssets/app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('dashboardAssets/app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('dashboardAssets/app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('dashboardAssets/app-assets/js/scripts/components.js') }}"></script>
    <!-- END: Theme JS-->
    <script>
        window.dashboardRequiredFieldMessage = @json(__('dashboard.required_field_message'));
    </script>
    <script src="{{ asset('dashboardAssets/custom/js/html-validation.js') }}"></script>

    <!-- BEGIN: Page JS-->
    <script>
        $(document).ready(function() {
            // Dark Mode Toggle
            const darkModeToggle = $('#dark-mode-toggle');
            const body = $('body');

            // Check if dark mode is saved in localStorage
            const isDarkMode = localStorage.getItem('darkMode') === 'true';

            // Apply dark mode if saved
            if (isDarkMode) {
                body.addClass('dark-mode');
                darkModeToggle.addClass('active');
                darkModeToggle.html('<i class="feather icon-sun"></i>');
            }

            // Toggle dark mode on button click
            darkModeToggle.on('click', function() {
                body.toggleClass('dark-mode');
                const isDark = body.hasClass('dark-mode');

                // Save preference to localStorage
                localStorage.setItem('darkMode', isDark);

                // Update button icon and state
                if (isDark) {
                    darkModeToggle.addClass('active');
                    darkModeToggle.html('<i class="feather icon-sun"></i>');
                } else {
                    darkModeToggle.removeClass('active');
                    darkModeToggle.html('<i class="feather icon-moon"></i>');
                }
            });
        });
    </script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>
