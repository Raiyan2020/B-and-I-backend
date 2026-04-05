@php use App\Models\GeneralSetting; @endphp
<div id="terms">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('dashboard.terms_conditions') }}</h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.generalSetting.terms.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="terms_ar">{{ __('dashboard.terms_ar') }}</label>
                                <textarea id="terms_ar" name="terms_ar" class="form-control ckeditor" dir="rtl" rows="10">{{ old('terms_ar', GeneralSetting::getValueForKey('terms_ar')) }}</textarea>
                                @error('terms_ar')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="terms_en">{{ __('dashboard.terms_en') }}</label>
                                <textarea id="terms_en" name="terms_en" class="form-control ckeditor" dir="ltr" rows="10">{{ old('terms_en', GeneralSetting::getValueForKey('terms_en')) }}</textarea>
                                @error('terms_en')
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
