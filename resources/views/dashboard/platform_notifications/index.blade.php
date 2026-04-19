<x-dashboard.layouts.master title="{{ __('dashboard.platform_notifications') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.platform_notifications') }}" />

            <div class="content-body">
                <section class="users-list-wrapper">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">{{ __('dashboard.platform_notifications') }}</h4>
                                </div>
                                <div class="card-body">
                                    @if (session('success'))
                                        <x-dashboard.layouts.message />
                                    @endif

                                    <form method="POST" action="{{ route('admin.platform-notifications.store') }}">
                                        @csrf

                                        <div class="form-group">
                                            <label for="title_ar">{{ __('dashboard.title_ar') }}</label>
                                            <input
                                                id="title_ar"
                                                type="text"
                                                name="title_ar"
                                                class="form-control @error('title_ar') is-invalid @enderror"
                                                value="{{ old('title_ar') }}"
                                                required
                                            >
                                            @error('title_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="title_en">{{ __('dashboard.title_en') }}</label>
                                            <input
                                                id="title_en"
                                                type="text"
                                                name="title_en"
                                                class="form-control @error('title_en') is-invalid @enderror"
                                                value="{{ old('title_en') }}"
                                                required
                                            >
                                            @error('title_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="body_ar">{{ __('dashboard.body_ar') }}</label>
                                            <textarea
                                                id="body_ar"
                                                name="body_ar"
                                                rows="4"
                                                class="form-control @error('body_ar') is-invalid @enderror"
                                                required
                                            >{{ old('body_ar') }}</textarea>
                                            @error('body_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="body_en">{{ __('dashboard.body_en') }}</label>
                                            <textarea
                                                id="body_en"
                                                name="body_en"
                                                rows="4"
                                                class="form-control @error('body_en') is-invalid @enderror"
                                                required
                                            >{{ old('body_en') }}</textarea>
                                            @error('body_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="send_to">{{ __('dashboard.send_to_group') }}</label>
                                            <select
                                                id="send_to"
                                                name="send_to"
                                                class="form-control select2 @error('send_to') is-invalid @enderror"
                                                required                                            >
                                                <option value="admins" @selected(old('send_to') === 'admins')>{{ __('dashboard.platform_notification_admins') }}</option>
                                                <option value="investors" @selected(old('send_to') === 'investors')>{{ __('dashboard.investors') }}</option>
                                                <option value="advertisers" @selected(old('send_to') === 'advertisers')>{{ __('dashboard.advertisers_companies') }}</option>
                                            </select>
                                            @error('send_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="feather icon-send mr-1"></i>{{ __('dashboard.send') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
