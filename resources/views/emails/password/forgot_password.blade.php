@component('mail::message')
# Helo $user->first_name,

Click on the button below to reset your password:

@component('mail::button', ['url' => $link])
Reset Password
@endcomponent

This password reset link will expire in {{ $expirationTime }}.

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent