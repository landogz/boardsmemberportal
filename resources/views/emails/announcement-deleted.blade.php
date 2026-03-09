<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Removed</title>
    <style>
        body, table, td, p { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { max-width: 100%; height: auto !important; border: 0; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .email-wrapper { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .email-header { background: linear-gradient(135deg, #055498 0%, #123a60 100%); padding: 30px 20px; text-align: center; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .email-body { padding: 40px 30px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 20px; line-height: 1.6; }
        .info-card { background-color: #f9fafb; border-left: 4px solid #055498; padding: 20px; margin: 25px 0; border-radius: 4px; }
        .footer { padding: 20px 30px; text-align: center; background-color: #f9fafb; font-size: 12px; color: #6b7280; }
        @media only screen and (max-width: 600px) {
            .email-body { padding: 24px 16px !important; }
            .email-header { padding: 24px 16px !important; }
            .email-header h1 { font-size: 20px !important; }
            .footer { padding: 16px 20px !important; }
            .info-card { padding: 16px !important; }
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
                            <h1>Announcement Removed</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <p class="greeting">Dear {{ $user->pre_nominal_title ?? '' }} {{ $user->last_name }},</p>
                            <p class="greeting">The following announcement has been removed from the Board Members Portal and is no longer available for viewing.</p>
                            <div class="info-card">
                                <p class="info-message" style="font-size: 14px; color: #4b5563; line-height: 1.7; margin: 0;"><strong>{{ $announcementTitle }}</strong></p>
                            </div>
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">Should you have any questions or require further assistance, kindly contact the Conference Secretariat through email at boardsec@ddb.gov.ph.</p>
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
