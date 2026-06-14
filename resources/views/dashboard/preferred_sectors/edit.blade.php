<x-dashboard.layouts.master title="{{ __('dashboard.edit preferred sector') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.edit preferred sector') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.preferred_sectors.index') }}">{{ __('dashboard.preferred sectors list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.edit preferred sector') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.preferred_sectors.update', $row->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="name-ar">{{ __('dashboard.table name') }} {{ __('dashboard.in arabic') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="name-ar"
                                                        value="{{ old('name.ar', $row->getTranslation('name', 'ar')) }}"
                                                        class="form-control" name="name[ar]"
                                                        placeholder="{{ __('dashboard.table name') }} {{ __('dashboard.in arabic') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-list"></i>
                                                    </div>
                                                </div>
                                                @error('name.ar')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="name-en">{{ __('dashboard.table name') }} {{ __('dashboard.in english') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="name-en"
                                                        value="{{ old('name.en', $row->getTranslation('name', 'en')) }}"
                                                        class="form-control" name="name[en]"
                                                        placeholder="{{ __('dashboard.table name') }} {{ __('dashboard.in english') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-list"></i>
                                                    </div>
                                                </div>
                                                @error('name.en')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="status">{{ __('dashboard.table status') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" id="status" name="status" required>
                                                        <option value="1" {{ old('status', $row->status) == 1 ? 'selected' : '' }}>{{ __('dashboard.active') }}</option>
                                                        <option value="0" {{ old('status', $row->status) == 0 ? 'selected' : '' }}>{{ __('dashboard.in-active') }}</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-slash"></i>
                                                    </div>
                                                </div>
                                                @error('status')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit"
                                                class="btn btn-primary mr-1 mb-1 submit_button">{{ __('dashboard.submit') }}</button>
                                            <a href="{{ route('admin.preferred_sectors.index') }}"
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
</x-dashboard.layouts.master>
