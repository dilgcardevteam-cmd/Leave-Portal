<x-mail::message>
# 🔐 Verify Your Email

Hi there,

Please use the one-time password (OTP) below to securely verify your email address and activate your account.

<x-mail::panel>
<div style="text-align:center;">
    <div style="font-size:12px; color:#6b7280; margin-bottom:6px;">
        YOUR VERIFICATION CODE
    </div>
    <div style="
        font-size:28px;
        font-weight:800;
        letter-spacing:6px;
        color:#0f172a;
    ">
        {{ $code }}
    </div>
    <div style="font-size:12px; color:#6b7280; margin-top:10px;">
        Expires in <strong>2 minutes</strong>
    </div>
</div>
</x-mail::panel>

For your security, **do not share this code with anyone**.  
If you didn’t request this verification, you can safely ignore this email.

Thanks,<br>
**{{ config('app.name') }}**

<x-slot:subcopy>
This is an automated message sent for security verification purposes.
</x-slot:subcopy>
</x-mail::message>