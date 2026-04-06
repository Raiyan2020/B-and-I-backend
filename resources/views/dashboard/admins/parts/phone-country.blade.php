@php
    /** @var \App\Models\Admin|null $row */
    $split = \App\Helpers\CountryHelper::splitDialCodeAndLocal(isset($row) ? $row->phone : null);
    $split['code'] = old('country_code', $split['code']);
    $split['local'] = old('phone_local', $split['local']);
    $countries = $countries ?? \App\Helpers\CountryHelper::getCountries();
@endphp
<div class="col-6">
    <div class="form-group admin-phone-field">
        <label for="admin-phone-local">{{ __('dashboard.table phone') }}</label>
        <div class="admin-phone-row">
            <div class="admin-country-wrap">
                <select name="country_code" id="admin-country-code" class="form-control select2-country" data-placeholder="{{ __('dashboard.choose_country_code') }}">
                    @foreach ($countries as $c)
                        @php
                            $flag = \App\Helpers\CountryHelper::flagEmojiFromIso($c['iso']);
                            $label = $flag.' '.$c['code'].' — '.\App\Helpers\CountryHelper::getCountryName($c);
                        @endphp
                        <option value="{{ $c['code'] }}" @selected($split['code'] === $c['code']) title="{{ \App\Helpers\CountryHelper::getCountryName($c) }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="admin-phone-local-wrap flex-grow-1">
                <input type="text" id="admin-phone-local" name="phone_local" class="form-control"
                    value="{{ $split['local'] }}"
                    placeholder="{{ __('dashboard.phone_without_code') }}"
                    inputmode="numeric" autocomplete="tel-national">
            </div>
        </div>
        @error('phone')
            <span class="text-danger small d-block mt-25">{{ $message }}</span>
        @enderror
    </div>
</div>

@push('page-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboardAssets/custom/css/admin-phone-input.css') }}">
@endpush

@push('page-scripts')
    <script>
        (function initAdminCountryPhoneSelect() {
            function run() {
                if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
                    setTimeout(run, 80);
                    return;
                }
                var $ = jQuery;
                var $sel = $('#admin-country-code');
                if (!$sel.length || $sel.data('select2-inited')) {
                    return;
                }
                $sel.data('select2-inited', true);
                var rtl = document.documentElement.getAttribute('data-textdirection') === 'rtl';
                $sel.select2({
                    width: '100%',
                    dir: rtl ? 'rtl' : 'ltr',
                    dropdownParent: $sel.closest('.card-body').length ? $sel.closest('.card-body') : $(document.body),
                    templateResult: function(state) {
                        if (!state.id) { return state.text; }
                        var t = state.text || '';
                        return $('<span class="admin-country-option-line"></span>').text(t);
                    },
                    templateSelection: function(state) {
                        if (!state.id) { return state.text; }
                        var t = state.text || '';
                        return $('<span class="admin-country-option-line"></span>').text(t);
                    }
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', run);
            } else {
                run();
            }
        })();
    </script>
@endpush
