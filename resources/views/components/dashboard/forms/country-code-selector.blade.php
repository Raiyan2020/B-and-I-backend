@props([
    'name' => 'country_code',
    'id' => 'country-code-selector',
    'value' => old('country_code', $value ?? ''),
    'phoneInputId' => 'phone',
    'required' => false,
    'col' => 'col-6',
])

@php
    $countries = \App\Helpers\CountryHelper::getCountries();
    $locale = app()->getLocale();
@endphp

<div class="{{ $col }}">
    <div class="form-group">
        <label for="{{ $id }}">{{ __('dashboard.country code') }}</label>
        <select name="{{ $name }}" id="{{ $id }}" class="form-control select2 country-code-selector" 
                style="width: 100%;" 
                data-phone-input="{{ $phoneInputId }}"
                {{ $required ? 'required' : '' }}>
            <option value="">{{ __('dashboard.choose country code') }}</option>
            @foreach($countries as $country)
                <option value="{{ $country['code'] }}" 
                        data-flag="{{ $country['iso'] }}" 
                        data-phone-start="{{ $country['phone_start'] }}"
                        {{ $value == $country['code'] ? 'selected' : '' }}>
                    {{ $country['code'] }} - {{ $locale === 'ar' ? $country['name_ar'] : $country['name_en'] }}
                </option>
            @endforeach
        </select>
        @error($name)
            <span class="text text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

