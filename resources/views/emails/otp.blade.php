<x-mail::message>
# Your OTP Code

Use the code below to verify your email and activate your account:

<x-mail::panel>
<h2 style="text-align:center; letter-spacing:4px; font-weight:700;">{{ $code }}</h2>
</x-mail::panel>

This code will expire in 2 minutes.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
