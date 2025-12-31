<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Approved</title>
    <style>
        /* Reset styles */
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
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        /* Main styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        .email-header {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
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
        
        .success-card {
            background-color: #f0fdf4;
            border-left: 4px solid #10B981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .success-title {
            font-size: 18px;
            font-weight: 600;
            color: #065f46;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        
        .success-message {
            font-size: 14px;
            color: #047857;
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
            background-color: #10B981;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
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
            background-color: #059669;
            background: linear-gradient(135deg, #059669 0%, #10B981 100%);
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
            .email-body {
                padding: 30px 20px;
            }
            
            .success-card {
                padding: 15px;
            }
            
            .success-title {
                font-size: 16px;
            }
            
            .button {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-wrapper">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                            <h1>✅ Registration Approved</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Hello <strong>{{ $user->pre_nominal_title }} {{ $user->first_name }} {{ $user->last_name }}</strong>,
                            </p>
                            
                            <p class="greeting">
                                Great news! Your registration has been approved. You can now access the Board Members Portal.
                            </p>
                            
                            <div class="success-card">
                                <h2 class="success-title">🎉 Welcome to Board Members Portal</h2>
                                <p class="success-message">
                                    Your account has been successfully approved and activated. You can now sign in using your registered credentials:
                                </p>
                                <p class="success-message" style="margin-top: 12px; font-weight: 600;">
                                    Username: <strong>{{ $user->username }}</strong>
                                </p>
                            </div>
                            
                            <div class="button-container">
                                <a href="{{ $loginUrl }}" class="button" style="background-color: #10B981; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">Sign In Now</a>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                If you have any questions or need assistance, please contact the administrator.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
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

