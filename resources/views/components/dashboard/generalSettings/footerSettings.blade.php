<?php use App\Models\GeneralSetting; ?>
<div class="card" id="footer">
    <div class="card-header">
        <h4 class="card-title">{{ __('home.socials') }}</h4>
    </div>
    <div class="card-content">
        <div class="card-body ">
            <form method="POST" action="{{ route('admin.generalSetting.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="socials">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="email-id-icon">{{ __('dashboard.twitter link') }}</label>
                            <div class="position-relative has-icon-left">
                                <input type="url" id="email-id-icon" class="form-control"
                                       name="type[twitter_links]"
                                       value="{{ old('type[twitter_links]', GeneralSetting::getValueForKey('twitter_links')) }}"
                                       placeholder="{{ __('dashboard.twitter link')}}">
                                <div class="form-control-position">
                                    <i class="feather icon-twitter"></i>
                                </div>
                            </div>
                            @error('type.twitter_links')
                            <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="email-id-icon">{{ __('dashboard.whatsapp_link') }}</label>
                            <div class="position-relative has-icon-left">
                                <input type="url" id="email-id-icon" class="form-control"
                                       name="type[whatsapp_link]"
                                       value="{{ old('type[whatsapp_link]', GeneralSetting::getValueForKey('whatsapp_link')) }}"
                                       placeholder="{{ __('dashboard.whatsapp_link')}}">
                                <div class="form-control-position">
                                    <i class="fa fa-whatsapp"></i>
                                </div>
                            </div>
                            @error('type.whatsapp_link')
                            <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="email-id-icon">{{ __('dashboard.snap_link') }}</label>
                            <div class="position-relative has-icon-left">
                                <input type="url" id="email-id-icon" class="form-control"
                                       name="type[snap_link]"
                                       value="{{ old('type[snap_link]', GeneralSetting::getValueForKey('snap_link')) }}"
                                       placeholder="{{ __('dashboard.snap_link')}}">
                                <div class="form-control-position">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 13px;" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path style="fill: rgba(34, 41, 47, 0.4);" d="M510.8 392.7c-5.2 12.2-27.2 21.1-67.4 27.3-2.1 2.8-3.8 14.7-6.5 24-1.6 5.6-5.6 8.9-12.1 8.9l-.3 0c-9.4 0-19.2-4.3-38.9-4.3-26.5 0-35.7 6-56.3 20.6-21.8 15.4-42.8 28.8-74 27.4-31.6 2.3-58-16.9-72.9-27.4-20.7-14.6-29.8-20.6-56.2-20.6-18.9 0-30.7 4.7-38.9 4.7-8.1 0-11.2-4.9-12.4-9-2.7-9.2-4.4-21.3-6.5-24.1-20.7-3.2-67.3-11.3-68.5-32.2a10.6 10.6 0 0 1 8.9-11.1c69.6-11.5 100.9-82.9 102.2-85.9 .1-.2 .2-.3 .2-.5 3.7-7.5 4.5-13.8 2.5-18.8-5.1-11.9-26.9-16.2-36.1-19.8-23.7-9.4-27-20.1-25.6-27.5 2.4-12.8 21.7-20.7 33-15.5 8.9 4.2 16.8 6.3 23.5 6.3 5 0 8.2-1.2 10-2.2-2-35.9-7.1-87.3 5.7-116C158.1 21.3 229.7 15.4 250.8 15.4c.9 0 9.1-.1 10.1-.1 52.1 0 102.3 26.8 126.7 81.6 12.8 28.7 7.7 79.8 5.7 116 1.6 .9 4.4 1.9 8.6 2.1 6.4-.3 13.8-2.4 22.1-6.3 6.1-2.8 14.4-2.5 20.5 .1l0 0c9.5 3.4 15.4 10.2 15.6 17.9 .2 9.7-8.5 18.2-25.9 25-2.1 .8-4.7 1.7-7.4 2.5-9.8 3.1-24.6 7.8-28.6 17.3-2.1 4.9-1.3 11.2 2.5 18.7 .1 .2 .2 .3 .2 .5 1.3 3 32.6 74.5 102.2 85.9 6.4 1.1 11.2 7.9 7.7 15.9z"/></svg>
                                </div>
                            </div>
                            @error('type.snap_link')
                            <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="email-id-icon">{{ __('dashboard.tiktok_link') }}</label>
                            <div class="position-relative has-icon-left">
                                <input type="url" id="email-id-icon" class="form-control"
                                       name="type[tiktok_link]"
                                       value="{{ old('type[tiktok_link]', GeneralSetting::getValueForKey('tiktok_link')) }}"
                                       placeholder="{{ __('dashboard.tiktok_link')}}">
                                <div class="form-control-position" >
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 13px;" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path style="fill: rgba(34, 41, 47, 0.4);" d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/></svg>
                                </div>
                            </div>
                            @error('type.tiktok_link')
                            <span class="text text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
                <x-dashboard.generalSettings.submitButton />
            </form>
        </div>
    </div>
</div>
