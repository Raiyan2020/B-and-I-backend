<x-dashboard.layouts.master title="{{ __('dashboard.add_subscription_package') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.add_subscription_package') }}">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.subscription_packages.index') }}">{{ __('dashboard.subscription_packages_list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('dashboard.add_subscription_package') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST"
                                action="{{ route('admin.subscription_packages.store') }}">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.package_name') }} {{ __('dashboard.in arabic') }}</label>
                                                <input type="text" class="form-control" name="name[ar]"
                                                    value="{{ old('name.ar') }}" required dir="rtl">
                                                @error('name.ar')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.package_name') }} {{ __('dashboard.in english') }}</label>
                                                <input type="text" class="form-control" name="name[en]"
                                                    value="{{ old('name.en') }}" required dir="ltr">
                                                @error('name.en')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.price_monthly') }} ({{ __('dashboard.kwd') }})</label>
                                                <input type="number" step="0.001" min="0" class="form-control"
                                                    name="price_monthly" value="{{ old('price_monthly') }}" required>
                                                @error('price_monthly')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.table status') }}</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="1" selected>{{ __('dashboard.active') }}</option>
                                                    <option value="0">{{ __('dashboard.in-active') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.description') }} {{ __('dashboard.in arabic') }}</label>
                                                <textarea id="description-ar" class="form-control ckeditor-package" name="description[ar]"
                                                    rows="8" dir="rtl" required>{{ old('description.ar') }}</textarea>
                                                @error('description.ar')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('dashboard.description') }} {{ __('dashboard.in english') }}</label>
                                                <textarea id="description-en" class="form-control ckeditor-package" name="description[en]"
                                                    rows="8" dir="ltr" required>{{ old('description.en') }}</textarea>
                                                @error('description.en')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1 submit_button">{{ __('dashboard.submit') }}</button>
                                            <button type="button" class="btn btn-outline-warning mr-1 mb-1 btn-reset-form">{{ __('dashboard.reset') }}</button>
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
        <link rel="stylesheet" href="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.css">
    @endpush
    @push('vendor-scripts')
        <script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>
    @endpush
    @push('page-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof CKEDITOR !== 'undefined') {
                    CKEDITOR.replace('description-ar', {
                        language: 'ar',
                        contentsLangDirection: 'rtl',
                        height: 320,
                        versionCheck: false
                    });
                    CKEDITOR.replace('description-en', {
                        language: 'en',
                        contentsLangDirection: 'ltr',
                        height: 320,
                        versionCheck: false
                    });
                }
            });
        </script>
    @endpush
</x-dashboard.layouts.master>
