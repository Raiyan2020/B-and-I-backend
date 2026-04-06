<x-dashboard.layouts.master title="{{ __('dashboard.edit_about_us_item') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.edit_about_us_item') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.about_us_items.index') }}">{{ __('dashboard.about_us_items_list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.edit_about_us_item') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.about_us_items.update', $row->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">

                                        <x-dashboard.generalSettings.uploadImage col="12" name="image" folder="about_us/" src="{{ $row->image }}" />

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="title-ar">{{ __('dashboard.title') }} {{ __('dashboard.in arabic') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="title-ar"
                                                        value="{{ old('title.ar', $row->getTranslation('title', 'ar')) }}"
                                                        class="form-control" name="title[ar]"
                                                        placeholder="{{ __('dashboard.title') }} {{ __('dashboard.in arabic') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-type"></i>
                                                    </div>
                                                </div>
                                                @error('title.ar')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="title-en">{{ __('dashboard.title') }} {{ __('dashboard.in english') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="title-en"
                                                        value="{{ old('title.en', $row->getTranslation('title', 'en')) }}"
                                                        class="form-control" name="title[en]"
                                                        placeholder="{{ __('dashboard.title') }} {{ __('dashboard.in english') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-type"></i>
                                                    </div>
                                                </div>
                                                @error('title.en')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="description-ar">{{ __('dashboard.description') }} {{ __('dashboard.in arabic') }}</label>
                                                <textarea id="description-ar"
                                                    class="form-control" name="description[ar]" rows="4"
                                                    placeholder="{{ __('dashboard.description') }} {{ __('dashboard.in arabic') }}" required>{{ old('description.ar', $row->getTranslation('description', 'ar')) }}</textarea>
                                                @error('description.ar')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="description-en">{{ __('dashboard.description') }} {{ __('dashboard.in english') }}</label>
                                                <textarea id="description-en"
                                                    class="form-control" name="description[en]" rows="4"
                                                    placeholder="{{ __('dashboard.description') }} {{ __('dashboard.in english') }}" required>{{ old('description.en', $row->getTranslation('description', 'en')) }}</textarea>
                                                @error('description.en')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="status-icon">{{ __('dashboard.table status') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control" name="status" required>
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
</x-dashboard.layouts.master>
