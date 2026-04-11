<x-mail::message>
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">

# {{ __('mail.verify_email.heading') }}

{{ __('mail.verify_email.greeting', ['name' => $user->name ?: __('mail.verify_email.user_fallback')]) }}

{{ __('mail.verify_email.intro') }}

{{ __('mail.verify_email.description') }}

<x-mail::panel>
{{ __('mail.verify_email.otp_label') }}

<div style="font-size: 28px; font-weight: bold; letter-spacing: 6px; text-align: center; margin-top: 12px;">
{{ $otp }}
</div>
</x-mail::panel>

{{ __('mail.verify_email.expiry_notice', ['minutes' => config('auth.verification.expire', 60)]) }}

{{ __('mail.verify_email.ignore') }}

{{ __('mail.verify_email.signature') }}<br>
{{ config('app.name') }}

</div>
</x-mail::message>
