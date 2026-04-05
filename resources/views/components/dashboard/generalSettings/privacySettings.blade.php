@php use App\Models\GeneralSetting; @endphp
<div id="privacy">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('dashboard.privacy_policy') }}</h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.generalSetting.privacy.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="privacy_policy_ar">{{ __('dashboard.privacy_policy_ar') }}</label>
                                <textarea id="privacy_policy_ar" name="privacy_policy_ar" class="form-control ckeditor" dir="rtl" rows="10">{{ old('privacy_policy_ar', GeneralSetting::getValueForKey('privacy_policy_ar')) }}</textarea>
                                @error('privacy_policy_ar')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="privacy_policy_en">{{ __('dashboard.privacy_policy_en') }}</label>
                                <textarea id="privacy_policy_en" name="privacy_policy_en" class="form-control ckeditor" dir="ltr" rows="10">{{ old('privacy_policy_en', GeneralSetting::getValueForKey('privacy_policy_en')) }}</textarea>
                                @error('privacy_policy_en')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary mr-1 mb-1">
                                <i class="feather icon-save mr-1"></i>{{ __('dashboard.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
