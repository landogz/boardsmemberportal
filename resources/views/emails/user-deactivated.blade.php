<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deactivated</title>
    <style>
        body, table, td, p { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .email-header { background: linear-gradient(135deg, #055498 0%, #123a60 100%); padding: 30px 20px; text-align: center; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .email-body { padding: 40px 30px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 20px; line-height: 1.6; }
        .info-card { background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 20px; margin: 25px 0; border-radius: 4px; }
        .footer { padding: 20px 30px; text-align: center; background-color: #f9fafb; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-wrapper">
                    <tr>
                        <td class="email-header">
                            <h1>Account Deactivated</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <p class="greeting">Dear {{ $user->first_name }} {{ $user->last_name }},</p>
                            <p class="greeting">Your Board Members Portal account has been deactivated. You will no longer be able to sign in or access the portal.</p>
                            <div class="info-card">
                                <p style="font-size: 14px; color: #4b5563; line-height: 1.7; margin: 0;">If you believe this was done in error or you need to regain access, please contact CONSEC or the system administrator.</p>
                            </div>
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">This is an automated notification from the Board Members Portal.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>© {{ date('Y') }} Board Members Portal. All rights reserved.</p>
                            <p style="margin-top: 8px;">This is an automated message. Please do not reply.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
