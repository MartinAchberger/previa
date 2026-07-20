<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PREVIA')</title>
</head>
<body style="margin:0;padding:0;background:#f4f2ee;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f2ee;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#ffffff;border:1px solid #e6e2da;">
                    <tr>
                        <td style="padding:28px 36px;border-bottom:1px solid #e6e2da;">
                            <span style="font-family:Georgia,'Times New Roman',serif;font-size:20px;letter-spacing:0.04em;color:#12110f;">PH&nbsp;LABORATORIES</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 36px;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;line-height:1.55;font-size:14px;">
                            @yield('body')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px;border-top:1px solid #e6e2da;font-family:Arial,Helvetica,sans-serif;color:#8a857b;font-size:11px;line-height:1.5;">
                            PREVIA Slovensko · V prípade otázok nás kontaktujte na {{ config('mail.contact_address') }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
