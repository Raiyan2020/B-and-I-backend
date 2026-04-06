<?php

use App\Models\GeneralSetting;

?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Saudia Ticket& Enriching The Human Thought">
    <meta name="keywords" content="Creative, Digital, multipage, landing, freelancer template">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="apple-touch-icon" href="{{ asset('dashboardAssets/app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon"
          href="{{ asset('Site/assets/images/logo/' . GeneralSetting::getValueForKey('favicon2')) }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    @if (app()->getLocale() == 'ar')
        <!-- BEGIN: Vendor CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/vendors/css/vendors-rtl.min.css') }}">
        <!-- END: Vendor CSS (Core)-->

        <!-- BEGIN: Theme CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css-rtl/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css-rtl/components.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css-rtl/themes/dark-layout.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css-rtl/themes/semi-dark-layout.css') }}">
        <!-- END: Theme CSS (Core)-->

        <!-- BEGIN: Page CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css-rtl/core/colors/palette-gradient.css') }}">
        <!-- END: Page CSS (Core)-->

        <!-- BEGIN: Custom CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css-rtl/custom-rtl.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/assets/css/style-rtl.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/settings-page.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/common-styles.css') }}">
        <!-- END: Custom CSS (Core)-->

    @else
        <!-- BEGIN: Vendor CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/vendors/css/vendors.min.css') }}">
        <!-- END: Vendor CSS (Core)-->

        <!-- BEGIN: Theme CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/components.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css/themes/dark-layout.css') }}">
        <!-- END: Theme CSS (Core)-->

        <!-- BEGIN: Page CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" type="text/css"
              href="{{ asset('dashboardAssets/app-assets/css/core/colors/palette-gradient.css') }}">
        <!-- END: Page CSS (Core)-->

        <!-- BEGIN: Custom CSS (Core - Loaded Globally)-->
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/custom.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/settings-page.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/app-assets/css/common-styles.css') }}">
        <!-- END: Custom CSS (Core)-->

    @endif

    <!-- SweetAlert2 CSS (Global - Used for flash messages)-->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Toastr (validation / admin notifications) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Dashboard Core CSS (Extracted from inline styles)-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/dashboard-core.css') }}">

    <!-- Table Toolkit CSS (Global - For checkbox selection and bulk actions)-->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/shared/table-toolkit.css') }}">

    <!-- Component Styles (Upload Image, DataTables)-->
    <x-dashboard.layouts.upload-image-style/>
    <x-dashboard.layouts.datatable-styles/>

    <!-- Stacks for page-specific and vendor styles -->
    @stack('vendor-styles')
    @stack('page-styles')
    @yield('styles')
    @stack('styles')
</head>
