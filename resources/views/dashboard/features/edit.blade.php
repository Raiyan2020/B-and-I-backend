<x-dashboard.layouts.master title="{{ __('dashboard.edit feature') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.edit feature') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.features.index') }}">{{ __('dashboard.features list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.edit feature') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.features.update', $row->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">

                                        <x-dashboard.generalSettings.uploadImage col="12" name="image" folder="features/" src="{{ $row->image }}" />

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="title-ar-icon">{{ __('dashboard.title in arabic') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="title-ar-icon"
                                                        value="{{ old('title.ar', $row->getTranslation('title', 'ar')) }}"
                                                        class="form-control" name="title[ar]"
                                                        placeholder="{{ __('dashboard.title in arabic') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-edit-2"></i>
                                                    </div>
                                                </div>
                                                @error('title.ar')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="title-en-icon">{{ __('dashboard.title in english') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="text" id="title-en-icon"
                                                        value="{{ old('title.en', $row->getTranslation('title', 'en')) }}"
                                                        class="form-control" name="title[en]"
                                                        placeholder="{{ __('dashboard.title in english') }}" required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-edit-2"></i>
                                                    </div>
                                                </div>
                                                @error('title.en')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description-ar-icon">{{ __('dashboard.description in arabic') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <textarea id="description-ar-icon"
                                                        class="form-control" name="description[ar]"
                                                        placeholder="{{ __('dashboard.description in arabic') }}"
                                                        rows="4" required>{{ old('description.ar', $row->getTranslation('description', 'ar')) }}</textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-align-left"></i>
                                                    </div>
                                                </div>
                                                @error('description.ar')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description-en-icon">{{ __('dashboard.description in english') }}</label>
                                                <div class="position-relative has-icon-left">
                                                    <textarea id="description-en-icon"
                                                        class="form-control" name="description[en]"
                                                        placeholder="{{ __('dashboard.description in english') }}"
                                                        rows="4" required>{{ old('description.en', $row->getTranslation('description', 'en')) }}</textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-align-left"></i>
                                                    </div>
                                                </div>
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
                                            <a href="{{ route('admin.features.index') }}"
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
