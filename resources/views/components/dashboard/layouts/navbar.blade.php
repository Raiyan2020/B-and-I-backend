<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                    class="ficon feather icon-menu"></i></a></li>
                    </ul>
                    <ul class="nav navbar-nav bookmark-icons">
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                                    class="ficon feather icon-maximize"></i></a></li>
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link" href="#" id="theme-toggle" title="{{ __('dashboard.toggle_theme') }}">
                                <i class="ficon feather icon-moon" id="theme-icon"></i>
                            </a>
                        </li>
                        <li class="dropdown dropdown-language nav-item">
                            <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false"><i
                                    class="flag-icon {{app()->getLocale() == 'ar'?'flag-icon-kw':'flag-icon-us'}}"></i><span
                                    class="selected-language">{{app()->getLocale() == 'ar'?'العربية':'English'}}</span></a>
                            <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                                <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('en') }}"
                                   data-language="en"><i class="flag-icon flag-icon-us"></i> English</a>
                                <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL('ar') }}"
                                   data-language="fr"><i class="flag-icon flag-icon-kw"></i> العربية</a>
                            </div>
                        </li>
                    </ul>

                </div>
                <ul class="nav navbar-nav float-right">


                    <x-notifications />
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                                                                   href="#" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none"><span
                                    class="user-name text-bold-600">{{auth('admin')->user()->name}}</span><span
                                    class="user-status">{{app()->getLocale() =='ar'? auth('admin')->user()->type_ar :auth('admin')->user()->type_en}}</span>
                            </div>
                            <span><img class="round" src="{{auth('admin')->user()->image}}" alt="avatar" height="40"
                                       width="40"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item"
                                                                          href="{{route('admin.profile')}}"><i
                                    class="feather icon-user"></i> {{__('dashboard.Edit Profile')}}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item js-admin-logout" href="{{route('admin.logout')}}"><i
                                    class="feather icon-power"></i> {{__('dashboard.Logout')}}</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
