<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('store.name', config('app.name')) }} verification code</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f2ec;font-family:Helvetica,Arial,sans-serif;color:#2a2a28;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" style="max-width:480px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #ece7db;">
                <tr>
                    <td style="padding:32px;text-align:center;">
                        <p style="margin:0 0 8px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#8a8578;">
                            {{ config('store.name', config('app.name')) }}
                        </p>
                        <h1 style="margin:0 0 16px;font-size:22px;">
                            {{ $purpose === 'register' ? 'Confirm your email' : 'Your sign-in code' }}
                        </h1>
                        <p style="margin:0 0 24px;font-size:14px;line-height:1.5;color:#57534e;">
                            {{ $purpose === 'register'
                                ? 'Enter this code to finish creating your account.'
                                : 'Enter this code to finish signing in.' }}
                        </p>
                        <p style="margin:0 0 24px;font-size:36px;font-weight:700;letter-spacing:.25em;color:#1a1a18;">
                            {{ $code }}
                        </p>
                        <p style="margin:0;font-size:13px;color:#8a8578;">
                            This code expires in 5 minutes. If you didn't request this, you can safely ignore this email.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
