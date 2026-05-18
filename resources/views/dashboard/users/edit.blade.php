<x-dashboard.layouts.master title="{{ $editTitle ?? __('dashboard.edit user') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ $editTitle ?? __('dashboard.edit user') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route($indexRouteName ?? 'admin.users.index') }}">{{ $listTitle ?? __('dashboard.users list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $editTitle ?? __('dashboard.edit user') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.users.update', $row->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="role" value="{{ $roleValue ?? $row->role?->value ?? '' }}">
                                <div class="form-body">
                                    <div class="row">

                                        <x-dashboard.generalSettings.uploadImage col="12" name="image" folder="users/" src="{{ $row->image }}" />

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="first-name-icon">{{ __('dashboard.table first name') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="first-name-icon"
                                                        value="{{ old('first_name', $row->first_name) }}"
                                                        class="form-control" name="first_name"
                                                        placeholder="{{ __('dashboard.table first name') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                                @error('first_name')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="last-name-icon">{{ __('dashboard.table last name') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="last-name-icon"
                                                        value="{{ old('last_name', $row->last_name) }}"
                                                        class="form-control" name="last_name"
                                                        placeholder="{{ __('dashboard.table last name') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                                @error('last_name')
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
                                                        name="phone" placeholder="{{ __('dashboard.table phone') }}" >
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
                                                <label for="lang-select">{{ __('dashboard.table language') }}</label>
                                                <select id="lang-select" name="lang" class="form-control" required>
                                                    <option value="">{{ __('dashboard.select language') }}</option>
                                                    <option value="ar" {{ old('lang', $row->lang) === 'ar' ? 'selected' : '' }}>{{ __('api.language_arabic') }}</option>
                                                    <option value="en" {{ old('lang', $row->lang) === 'en' ? 'selected' : '' }}>{{ __('api.language_english') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        @if(($roleValue ?? null) === \App\Enums\UserRole::Investor->value)
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.investor_type') }}</label>
                                                    <select name="investor_type" class="form-control" required>
                                                        <option value="">{{ __('dashboard.choose option') }}</option>
                                                        @foreach($investorTypes ?? [] as $investorType)
                                                            <option value="{{ $investorType->value }}" {{ old('investor_type', $row->investor_type?->value ?? $row->investor_type) === $investorType->value ? 'selected' : '' }}>
                                                                {{ __('enums.investor_type.'.$investorType->value) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.capital') }}</label>
                                                    <input type="number" step="0.001" min="1000" class="form-control" name="capital" value="{{ old('capital', $row->capital) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.available_capital') }}</label>
                                                    <input type="number" step="0.001" min="1000" class="form-control" name="available_capital" value="{{ old('available_capital', $row->available_capital) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.preferred_sectors') }}</label>
                                                    <select name="preferred_sector_id" class="form-control" required>
                                                        <option value="">{{ __('dashboard.choose option') }}</option>
                                                        @foreach($preferredSectors ?? [] as $preferredSector)
                                                            <option value="{{ $preferredSector->id }}" {{ (string) old('preferred_sector_id', $row->preferred_sector_id) === (string) $preferredSector->id ? 'selected' : '' }}>
                                                                {{ $preferredSector->getTranslation('name', app()->getLocale()) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.category') }}</label>
                                                    <select name="category_id" class="form-control" required>
                                                        <option value="">{{ __('dashboard.choose option') }}</option>
                                                        @foreach($categories ?? [] as $category)
                                                            <option value="{{ $category->id }}" {{ (string) old('category_id', $row->category_id) === (string) $category->id ? 'selected' : '' }}>
                                                                {{ $category->getTranslation('name', app()->getLocale()) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.experience_level') }}</label>
                                                    <input type="number" step="0.001" min="0" max="100" class="form-control" name="experience_level" value="{{ old('experience_level', $row->experience_level) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.previous_investments_count') }}</label>
                                                    <input type="number" min="0" class="form-control" name="previous_investments_count" value="{{ old('previous_investments_count', $row->previous_investments_count) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>{{ __('dashboard.investor_experience') }}</label>
                                                    <select name="investor_experience" class="form-control" required>
                                                        <option value="">{{ __('dashboard.choose option') }}</option>
                                                        @foreach($investorExperiences ?? [] as $investorExperience)
                                                            <option value="{{ $investorExperience->value }}" {{ old('investor_experience', $row->investor_experience?->value ?? $row->investor_experience) === $investorExperience->value ? 'selected' : '' }}>
                                                                {{ __('enums.investor_experience.'.$investorExperience->value) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="company-license">{{ __('dashboard.table company license') }}</label>
                                                    <div class="position-relative has-icon-left">
                                                        <input type="file" id="company-license" class="form-control" name="company_license" accept="image/jpeg,image/png,image/jpg,application/pdf">
                                                        <div class="form-control-position">
                                                            <i class="feather icon-file"></i>
                                                        </div>
                                                    </div>
                                                    @error('company_license')
                                                        <span class="text text-danger">{{ $message }}</span>
                                                    @enderror
                                                    @if($row->company_license_url)
                                                        <div class="mt-2">
                                                            <a href="{{ $row->company_license_url }}" target="_blank">{{ __('dashboard.view current license') }}</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="password-icon">{{ __('dashboard.table email') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="email" class="form-control"
                                                        value="{{ old('email', $row->email) }}"
                                                        name="email" placeholder="{{ __('dashboard.table email') }}" required>
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
