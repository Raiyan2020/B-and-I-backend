@php use App\Models\Role; @endphp
<x-dashboard.layouts.master title="{{ __('dashboard.edit admin') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.edit admin') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.admins.index') }}">{{ __('dashboard.admins list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.edit admin') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.admins.update', $row->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">

                                        <x-dashboard.generalSettings.uploadImage col="12" name="image" folder="admins/" src="{{ $row->image }}" />

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="first-name-icon">{{ __('dashboard.role name') }}</label>
                                                <select name="role" class="select2 form-control" required>
                                                    <option disabled>{{ __('dashboard.choose role') }}</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->name }}"
                                                            {{ $row->hasRole($role->name) ? 'selected' : '' }}>
                                                            {{ $role->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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

                                        @include('dashboard.admins.parts.phone-country', ['row' => $row])
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
                                            <button type="reset"
                                                class="btn btn-outline-warning mr-1 mb-1">{{ __('dashboard.reset') }}</button>
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
        <!-- Select2 CSS (Page-specific)-->
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/vendors/css/forms/select/select2.min.css') }}">
    @endpush

    @push('vendor-scripts')
        <!-- Select2 JS (Page-specific)-->
        <script src="{{ asset('dashboardAssets/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
        <script src="{{ asset('dashboardAssets/app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    @endpush
</x-dashboard.layouts.master>
