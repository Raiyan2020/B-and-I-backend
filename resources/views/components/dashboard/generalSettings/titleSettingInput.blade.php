@php use App\Models\GeneralSetting; @endphp
<div class="col-6">
    <div class="form-group">
        <label for="type[footer_description_ar]">{{__('dashboard.footer_description').' '.__('dashboard.in arabic')}}</label>
        <input type="text" class="form-control" name="type[{{GeneralSetting::getValueForKey($nav.'_en').'_title_ar'}}]" value="{{old("type[".GeneralSetting::getValueForKey($nav.'en')."_title_ar]",GeneralSetting::getValueForKey(GeneralSetting::getValueForKey($nav.'_en').'_title_ar'))}}"  placeholder="{{__('dashboard.title').' '.GeneralSetting::getValueForKey($nav.'_'.app()->getLocale()).' '.__('dashboard.in arabic')}}" required >
        @error('type.'.$nav.'_title_ar')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
</div>
<div class="col-6">
    <div class="form-group">
        <label for="type[footer_description_en]">{{__('dashboard.title').' '.GeneralSetting::getValueForKey($nav.'_'.app()->getLocale()).' '.__('dashboard.in english')}}</label>
        <input type="text" class="form-control" name="type[{{GeneralSetting::getValueForKey($nav.'_en').'_title_en'}}]" value="{{old("type[".GeneralSetting::getValueForKey($nav.'en')."_title_en]",GeneralSetting::getValueForKey(GeneralSetting::getValueForKey($nav.'_en').'_title_en'))}}"  placeholder="{{__('dashboard.title').' '.GeneralSetting::getValueForKey($nav.'_'.app()->getLocale()).' '.__('dashboard.in english')}}" required >
        @error('type.'.$nav.'_title_en')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
</div>
