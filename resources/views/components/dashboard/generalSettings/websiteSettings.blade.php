@php use App\Models\GeneralSetting;
    $logo1 = asset('Site/assets/images/logo/' . GeneralSetting::getValueForKey('logo1'));
    $favicon2 = asset('Site/assets/images/logo/' . GeneralSetting::getValueForKey('favicon2'));
@endphp
<div id="general-settings">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('dashboard.general settings') }}</h4>
        </div>
        <div class="card-content">
            <div class="card-body ">
                <form method="POST" action="{{ route('admin.generalSetting.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="generalSettings">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label
                                    for="type['website_name_ar']">{{ __('dashboard.website name') . ' ' . __('dashboard.in arabic') }}</label>
                                <input type="text" class="form-control" name="type[website_name_ar]"
                                    value="{{ old('type.website_name_ar', GeneralSetting::getValueForKey('website_name_ar')) }}"
                                    placeholder="{{ __('dashboard.website name') . ' ' . __('dashboard.in arabic') }}">
                                @error('type.website_name_ar')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label
                                    for="type['website_name_en']">{{ __('dashboard.website name') . ' ' . __('dashboard.in english') }}</label>
                                <input type="text" class="form-control" name="type[website_name_en]"
                                    value="{{ old('type.website_name_en', GeneralSetting::getValueForKey('website_name_en')) }}"
                                    placeholder="{{ __('dashboard.website name') . ' ' . __('dashboard.in english') }}"
                                    required>
                                @error('type.website_name_en')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="type['contact_number']">{{ __('dashboard.contact number') }}</label>
                                <input type="number" class="form-control" name="type[contact_number]"
                                    value="{{ old('type.contact_number', GeneralSetting::getValueForKey('contact_number')) }}"
                                    placeholder="{{ __('dashboard.contact number') }}" required>
                                @error('type.contact_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="type['contact_number']">{{ __('dashboard.contact mail') }}</label>
                                <input type="email" class="form-control" name="type[contact_mail]"
                                    value="{{ old('type.contact_mail', GeneralSetting::getValueForKey('contact_mail')) }}"
                                    placeholder="{{ __('dashboard.contact mail') }}" required>
                                @error('type.contact_mail')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="type['contact_number']">{{ __('home.commercial_register') }}</label>
                                <input type="number" class="form-control" name="type[commercial_register]"
                                    value="{{ old('type.commercial_register', GeneralSetting::getValueForKey('commercial_register')) }}"
                                    placeholder="{{ __('home.commercial_register') }}" required>
                                @error('type.commercial_register')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="type['contact_number']">{{ __('home.tax_number') }}</label>
                                <input type="number" class="form-control" name="type[tax_number]"
                                    value="{{ old('type.tax_number', GeneralSetting::getValueForKey('tax_number')) }}"
                                    placeholder="{{ __('home.tax_number') }}" required>
                                @error('type.tax_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="type['contact_number']">{{ __('home.copy_right') }}</label>
                                <input type="text" class="form-control" name="type[copy_right]"
                                    value="{{ old('type.copy_right', GeneralSetting::getValueForKey('copy_right')) }}"
                                    placeholder="{{ __('home.copy_right') }}" required>
                                @error('type.copy_right')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <x-dashboard.generalSettings.uploadImage i="1" name="logo" folder="logo/" src="{{ $logo1 }}"  />
                        <x-dashboard.generalSettings.uploadImage i="2" name="favicon" folder="logo/" src="{{ $favicon2 }}"  />
                    </div>
                    <x-dashboard.generalSettings.submitButton id="general-settings-save" />
                </form>
            </div>
        </div>
    </div>
</div>
