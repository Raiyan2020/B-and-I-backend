<x-dashboard.layouts.master title="{{ __('dashboard.edit user') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.edit user') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.users.index') }}">{{ __('dashboard.users list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.edit user') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.users.update', $row->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">

                                        <x-dashboard.generalSettings.uploadImage col="12" name="image" folder="users/" src="{{ $row->image }}" />

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="first-name-icon">{{ __('dashboard.table name') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="first-name-icon"
                                                        value="{{ old('name', $row->name) }}"
                                                        class="form-control" name="name"
                                                        placeholder="{{ __('dashboard.table name') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-grid"></i>
                                                    </div>
                                                </div>
                                                @error('name')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <x-dashboard.forms.country-code-selector 
                                            name="country_code"
                                            id="country-code-selector"
                                            :value="old('country_code', $row->country_code)"
                                            phone-input-id="phone-input"
                                            col="col-6"
                                        />

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="phone-input">{{ __('dashboard.table phone') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="phone-input" class="form-control phone-input"
                                                        value="{{ old('phone', $row->phone) }}"
                                                        name="phone" placeholder="{{ __('dashboard.table phone') }}">
                                                    <div class="form-control-position">
                                                        <i class="fa fa-phone"></i>
                                                    </div>
                                                </div>
                                                @error('phone')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="password-icon">{{ __('dashboard.table email') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="email" class="form-control"
                                                        value="{{ old('email', $row->email) }}"
                                                        name="email" placeholder="{{ __('dashboard.table email') }}">
                                                    <div class="form-control-position">
                                                        <i class="fa fa-envelope"></i>
                                                    </div>
                                                </div>
                                                @error('email')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="password-icon">{{ __('dashboard.table password') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="password" class="form-control" name="password"
                                                        placeholder="{{ __('dashboard.leave blank to keep current password') }}">
                                                    <div class="form-control-position">
                                                        <i class="fa fa-pencil"></i>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label
                                                    for="password-icon">{{ __('dashboard.table confirm password') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="password" class="form-control"
                                                        name="password_confirmation"
                                                        placeholder="{{ __('dashboard.table confirm password') }}">
                                                    <div class="form-control-position">
                                                        <i class="fa fa-pencil"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit"
                                                class="btn btn-primary mr-1 mb-1 submit_button">{{ __('dashboard.submit') }}</button>
                                            <a href="{{ route('admin.users.index') }}"
                                                class="btn btn-outline-secondary mr-1 mb-1">{{ __('dashboard.cancel') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('vendor-styles')
        <!-- Select2 CSS -->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/vendors/css/forms/select/select2.min.css') }}">
        <!-- Flag Icon CSS -->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/fonts/flag-icon-css/css/flag-icon.min.css') }}">
    @endpush

    @push('vendor-scripts')
        <!-- Select2 JS -->
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
        <script>
            // Pass translations to JS (with :start placeholder to be replaced in JS)
            window.dashboardTranslations = {
                phone_must_start_with: '{{ __('dashboard.phone_must_start_with') }}',
                table_phone: '{{ __('dashboard.table phone') }}'
            };
        </script>
        <script src="{{ asset('dashboardAssets/custom/js/shared/country-code-selector.js') }}"></script>
    @endpush
</x-dashboard.layouts.master>
