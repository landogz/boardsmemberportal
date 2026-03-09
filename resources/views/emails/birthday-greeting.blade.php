<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Birthday</title>
    <style>
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            height: auto !important;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .email-wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #055498 0%, #123a60 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            color: #374151;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .birthday-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%);
            border-left: 4px solid #055498;
            padding: 24px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
        }
        .birthday-card p {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            line-height: 1.5;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #055498 0%, #123a60 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
            line-height: 1.6;
        }
        @media only screen and (max-width: 600px) {
            .email-body { padding: 24px 16px !important; }
            .email-header { padding: 24px 16px !important; }
            .email-header h1 { font-size: 20px !important; }
            .footer { padding: 16px 20px !important; }
            .birthday-card { padding: 18px !important; }
            .birthday-card p { font-size: 16px !important; }
            .button { display: block !important; width: 100% !important; max-width: 100%; box-sizing: border-box; padding: 14px 16px !important; font-size: 16px !important; }
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-wrapper" style="max-width: 100%; width: 100%;">
                    <tr>
                        <td class="email-header">
                            <h1>🎂 Happy Birthday!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Dear {{ $user->pre_nominal_title ?? '' }} <strong>{{ $user->short_name }}</strong>,
                            </p>
                            <p class="greeting">
                                The Board Members Portal team wishes you a very happy birthday! We hope your special day is filled with joy, good health, and wonderful moments with your loved ones.
                            </p>
                            <div class="birthday-card">
                                <p>Thank you for being part of our community. We appreciate your continued engagement and wish you all the best in the year ahead.</p>
                            </div>
                            <div class="button-container">
                                <a href="{{ rtrim(config('app.url'), '/') }}/login" class="button">Sign in to the Portal</a>
                            </div>
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                Warm regards,<br>
                                <strong>Board Members Portal</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p class="footer-text">© {{ date('Y') }} Board Members Portal. All rights reserved.</p>
                            <p class="footer-text" style="margin-top: 8px;">This is an automated birthday greeting. Please do not reply to this message.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
