@php use App\Models\GeneralSetting; @endphp
<x-dashboard.layouts.master title="{{ __('dashboard.general settings') }}">
    <!-- BEGIN: Content-->
    <div class="app-content content settings-page">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users list start -->
                <section class="users-list-wrapper">
                    <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.manage general settings') }}">
                    </x-dashboard.layouts.breadcrumb>
                    <!-- Column selectors with Export Options and print table -->
                    <section id="column-selectors">
                        @if (\Session::get('success'))
                            <x-dashboard.layouts.message />
                        @endif
                        <div class="row">
                            <!-- Sidebar Navigation -->
                            <div class="col-12 col-md-3 col-lg-3 settings-sidebar-col">
                                <div class="settings-sidebar-placeholder"></div>
                                <div class="settings-sidebar-wrapper">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body p-2">
                                                <div class="main-menu-content">
                                                    <ul class="nav nav-pills flex-column" id="settings-nav">
                                                        <li class="nav-item mb-1">
                                                            <a class="nav-link settings-nav-link active"
                                                                href="#general-settings"
                                                                data-section="general-settings">
                                                                <i class="feather icon-home mr-1"></i>
                                                                <span>{{ __('dashboard.general settings') }}</span>
                                                            </a>
                                                        </li>
                                                        <li class="nav-item mb-1">
                                                            <a class="nav-link settings-nav-link" href="#footer"
                                                                data-section="footer">
                                                                <i class="feather icon-navigation mr-1"></i>
                                                                <span>{{ __('home.socials') }}</span>
                                                            </a>
                                                        </li>
                                                        <li class="nav-item mb-1">
                                                            <a class="nav-link settings-nav-link" href="#terms"
                                                                data-section="terms">
                                                                <i class="feather icon-file-text mr-1"></i>
                                                                <span>{{ __('dashboard.terms_conditions') }}</span>
                                                            </a>
                                                        </li>
                                                        <li class="nav-item mb-1">
                                                            <a class="nav-link settings-nav-link" href="#privacy"
                                                                data-section="privacy">
                                                                <i class="feather icon-shield mr-1"></i>
                                                                <span>{{ __('dashboard.privacy_policy') }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Main Content -->
                            <div class="col-12 col-md-9 col-lg-9">
                                <div class="settings-sections">
                                    <x-dashboard.generalSettings.websiteSettings />
                                    <x-dashboard.generalSettings.footerSettings />
                                    <x-dashboard.generalSettings.termsSettings />
                                    <x-dashboard.generalSettings.privacySettings />
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Column selectors with Export Options and print table -->
                </section>
                <!-- users list ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    @push('vendor-styles')
        <!-- CKEditor CSS -->
        <link rel="stylesheet" href="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.css">
    @endpush

    @push('page-scripts')
        <script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>
        <script>
            @foreach (['ar', 'en'] as $lang)
                CKEDITOR.replace('terms_{{ $lang }}', {
                    versionCheck: false
                });
                CKEDITOR.replace('privacy_policy_{{ $lang }}', {
                    versionCheck: false
                });

            @endforeach
        </script>
        <script>
            $(document).ready(function() {
                // Initialize CKEditor instances (will be initialized when section is shown)
                var termsArEditor, termsEnEditor, privacyArEditor, privacyEnEditor;
                var termsArInitialized = false,
                    termsEnInitialized = false;
                var privacyArInitialized = false,
                    privacyEnInitialized = false;

                // Settings navigation handler
                $('#settings-nav .settings-nav-link').on('click', function(e) {
                    e.preventDefault();
                    var $link = $(this);
                    var sectionId = $link.data('section') || $link.attr('href').substring(1);

                    // Update URL hash
                    window.location.hash = sectionId;

                    // Switch to section
                    switchToSection(sectionId);
                });

                // Function to switch to a section
                function switchToSection(sectionId) {
                    // Remove active class from all links
                    $('#settings-nav .settings-nav-link').removeClass('active');
                    // Add active class to the link for this section
                    $('#settings-nav .settings-nav-link[data-section="' + sectionId + '"]').addClass('active');

                    // Hide all sections
                    $('.settings-sections > div[id]').hide();

                    // Show selected section
                    $('#' + sectionId).show();

                    // Initialize CKEditor when section is shown
                    if (sectionId === 'terms') {
                        if (!termsArInitialized && typeof CKEDITOR !== 'undefined') {
                            termsArEditor = CKEDITOR.replace('terms_ar', {
                                language: 'ar',
                                contentsLangDirection: 'rtl',
                                height: 400
                            });
                            termsArInitialized = true;
                        }
                        if (!termsEnInitialized && typeof CKEDITOR !== 'undefined') {
                            termsEnEditor = CKEDITOR.replace('terms_en', {
                                language: 'en',
                                contentsLangDirection: 'ltr',
                                height: 400
                            });
                            termsEnInitialized = true;
                        }
                    } else if (sectionId === 'privacy') {
                        if (!privacyArInitialized && typeof CKEDITOR !== 'undefined') {
                            privacyArEditor = CKEDITOR.replace('privacy_policy_ar', {
                                language: 'ar',
                                contentsLangDirection: 'rtl',
                                height: 400
                            });
                            privacyArInitialized = true;
                        }
                        if (!privacyEnInitialized && typeof CKEDITOR !== 'undefined') {
                            privacyEnEditor = CKEDITOR.replace('privacy_policy_en', {
                                language: 'en',
                                contentsLangDirection: 'ltr',
                                height: 400
                            });
                            privacyEnInitialized = true;
                        }
                    }

                    // Smooth scroll to section
                    setTimeout(function() {
                        var offset = $('#' + sectionId).offset();
                        if (offset) {
                            var scrollto = offset.top - 80;
                            $('html, body').animate({
                                scrollTop: scrollto
                            }, 300);
                        }
                    }, 100);
                }

                // Check URL hash on page load
                var hash = window.location.hash.substring(1); // Remove #
                if (hash && ['general-settings', 'footer', 'terms', 'privacy'].includes(hash)) {
                    switchToSection(hash);
                } else {
                    // Initialize on page load - show active section
                    var activeSection = $('#settings-nav .settings-nav-link.active').data('section') ||
                        'general-settings';
                    switchToSection(activeSection);
                }
            });
        </script>
    @endpush

</x-dashboard.layouts.master>
