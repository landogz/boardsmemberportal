<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONSEC Account Created</title>
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
        .info-card {
            background-color: #eff6ff;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-title {
            font-size: 18px;
            font-weight: 600;
            color: #1d4ed8;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        .info-message {
            font-size: 14px;
            color: #1f2937;
            line-height: 1.7;
            margin: 0;
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
            line-height: 1.5;
            text-align: center;
            border: none;
        }
        .button:hover {
            background: linear-gradient(135deg, #123a60 0%, #055498 100%);
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
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 25px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-body { padding: 24px 16px !important; }
            .email-header { padding: 24px 16px !important; }
            .email-header h1 { font-size: 20px !important; }
            .footer { padding: 16px 20px !important; }
            .info-card { padding: 16px !important; }
            .info-title { font-size: 16px !important; }
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
                            <h1>CONSEC Account Created</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Dear {{ $user->pre_nominal_title ?? '' }} {{ $user->first_name }} {{ $user->last_name }},
                            </p>

                            <p class="greeting">
                                Your CONSEC account for the Boards Member Portal has been created. You can now sign in and manage Board Member registrations, notices, and other CONSEC functions.
                            </p>

                            <div class="info-card">
                                <h2 class="info-title">Your account details</h2>
                                <p class="info-message">
                                    Below are your key account details:
                                </p>
                                <p class="info-message" style="margin-top: 12px;">
                                    Username: <strong>{{ $user->username }}</strong><br>
                                    Email: <strong>{{ $user->email }}</strong>
                                </p>
                                <p class="info-message" style="margin-top: 12px; font-size: 13px; color: #4b5563;">
                                    For security reasons, your password is not shown here. Please use the password you set during account creation. If you ever forget it, you can reset it using the "Forgot password" link on the login page.
                                </p>
                            </div>

                            <div class="button-container">
                                <a href="{{ $loginUrl }}" class="button">Go to Login</a>
                            </div>

                            <div class="divider"></div>

                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                If you did not expect this account or believe this was created in error, please contact the system administrator immediately.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p class="footer-text">
                                © {{ date('Y') }} Board Members Portal. All rights reserved.
                            </p>
                            <p class="footer-text" style="margin-top: 8px;">
                                This is an automated email. Please do not reply to this message.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

