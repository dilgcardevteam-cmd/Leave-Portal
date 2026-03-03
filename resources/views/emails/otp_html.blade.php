<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>Your OTP Code</title>

<style>
    body, table, td, a {
        -webkit-text-size-adjust:100%;
        -ms-text-size-adjust:100%;
    }
    table { border-collapse:collapse !important; }

    body {
        margin:0;
        padding:0;
        width:100% !important;
        height:100% !important;
        background:#f3f4f6;
        font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color:#111827;
    }

    .container {
        max-width:560px;
        margin:0 auto;
        padding:32px 16px;
    }

    .card {
        background:#ffffff;
        border-radius:18px;
        overflow:hidden;
        box-shadow:0 10px 25px rgba(0,0,0,.08);
    }

    /* HEADER */
    .header {
        background:linear-gradient(135deg,#1d4ed8,#0f172a);
        padding:24px;
        color:#ffffff;
    }

    .brand {
        font-size:18px;
        font-weight:700;
    }

    .subtitle {
        font-size:14px;
        opacity:.9;
        margin-top:4px;
    }

    /* CONTENT */
    .content {
        padding:32px 24px;
        text-align:center;
    }

    .title {
        font-size:22px;
        font-weight:700;
        margin:0 0 8px;
    }

    .muted {
        font-size:14px;
        color:#6b7280;
        margin:0 0 24px;
    }

    /* OTP */
    .otp-box {
        background:#f9fafb;
        border:1px dashed #cbd5e1;
        border-radius:14px;
        padding:20px;
        margin:0 auto 20px;
        display:inline-block;
        min-width:220px;
    }

    .otp-code {
        font-size:30px;
        font-weight:800;
        letter-spacing:8px;
        color:#0f172a;
    }

    .expire {
        font-size:13px;
        color:#6b7280;
        margin-top:8px;
    }

    /* FOOTER */
    .footer {
        background:#f9fafb;
        border-top:1px solid #e5e7eb;
        padding:18px 24px;
        font-size:12px;
        color:#6b7280;
        text-align:center;
    }
</style>
</head>

<body>
<div class="container">
<div class="card">

    <!-- HEADER -->
    <div class="header">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <!-- LEFT TEXT -->
                <td style="vertical-align:middle;">
                    <div class="brand">Leave Application System</div>
                    <div class="subtitle">Secure account verification</div>
                </td>

                <!-- RIGHT LOGO -->
                <td style="vertical-align:middle; text-align:right;">
                    <img
                        src="{{ $logoUrl }}"
                        alt="DILG Logo"
                        width="64"
                        style="display:block; border:0; outline:none; text-decoration:none;"
                    >
                </td>
            </tr>
        </table>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <h1 class="title">Verify Your Email</h1>
        <p class="muted">
            Use the one-time password below to complete your sign-in.
        </p>

        <div class="otp-box">
            <div class="otp-code">{{ $code }}</div>
            <div class="expire">Expires in 2 minutes</div>
        </div>

        <p class="muted">
            If you didn’t request this code, you can safely ignore this email.
        </p>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        © {{ date('Y') }} {{ $appName }}<br>
        This is an automated message — please do not reply.
    </div>

</div>
</div>
</body>
</html>
