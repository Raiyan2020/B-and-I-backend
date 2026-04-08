<x-mail::message>
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">

# {{ __('mail.verify_email.heading') }}

{{ __('mail.verify_email.greeting', ['name' => $user->name ?: __('mail.verify_email.user_fallback')]) }}

{{ __('mail.verify_email.intro') }}

{{ __('mail.verify_email.description') }}

<x-mail::button :url="$actionUrl" color="primary">
{{ __('mail.verify_email.action') }}
</x-mail::button>

{{ __('mail.verify_email.fallback') }}

[{{ $actionUrl }}]({{ $actionUrl }})

{{ __('mail.verify_email.ignore') }}

{{ __('mail.verify_email.signature') }}<br>
{{ config('app.name') }}

</div>
</x-mail::message>
