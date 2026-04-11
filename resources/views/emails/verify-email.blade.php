<x-mail::message>
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">

# {{ __('mail.' . $mailSection . '.heading') }}

{{ __('mail.' . $mailSection . '.greeting', ['name' => $userName]) }}

{{ __('mail.' . $mailSection . '.intro') }}

{{ __('mail.' . $mailSection . '.description') }}

<x-mail::panel>
{{ __('mail.' . $mailSection . '.otp_label') }}

<div style="font-size: 28px; font-weight: bold; letter-spacing: 6px; text-align: center; margin-top: 12px;">
{{ $otp }}
</div>
</x-mail::panel>

{{ __('mail.' . $mailSection . '.expiry_notice', ['minutes' => config('auth.verification.expire', 60)]) }}

{{ __('mail.' . $mailSection . '.ignore') }}

{{ __('mail.' . $mailSection . '.signature') }}<br>
{{ config('app.name') }}

</div>
</x-mail::message>
