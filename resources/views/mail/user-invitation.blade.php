@component('mail::message')
# {{ __('Set your password') }}

{{ __('Hello :name,', ['name' => $userName]) }}

{{ __('An administrator has created an account for you on :app. Click the button below to set your password and activate your account.', ['app' => config('app.name')]) }}

@component('mail::button', ['url' => $setPasswordUrl])
{{ __('Set Password') }}
@endcomponent

{{ __('This invitation link will expire on :date.', ['date' => $expiresAt?->format('M d, Y H:i')]) }}

{{ __('If you did not expect this invitation, you can safely ignore this email.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
