<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Not Approved</title>
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
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
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
        
        .rejection-card {
            background-color: #fef2f2;
            border-left: 4px solid #EF4444;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .rejection-title {
            font-size: 18px;
            font-weight: 600;
            color: #991b1b;
            margin: 0 0 15px 0;
            line-height: 1.4;
        }
        
        .rejection-message {
            font-size: 14px;
            color: #7f1d1d;
            line-height: 1.7;
            margin: 0 0 15px 0;
        }
        
        .reason-box {
            background-color: #ffffff;
            border: 1px solid #fecaca;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .reason-label {
            font-size: 12px;
            font-weight: 600;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .reason-text {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
            margin: 0;
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
            
            .rejection-card {
                padding: 15px;
            }
            
            .rejection-title {
                font-size: 16px;
            }
            
            .reason-box {
                padding: 12px;
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
                            <h1>❌ Registration Not Approved</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Hello <strong>{{ $userName }}</strong>,
                            </p>
                            
                            <p class="greeting">
                                We regret to inform you that your registration request for the Board Members Portal has not been approved.
                            </p>
                            
                            <div class="rejection-card">
                                <h2 class="rejection-title">Registration Status</h2>
                                <p class="rejection-message">
                                    After careful review, we are unable to approve your registration at this time.
                                </p>
                                
                                @if($rejectionReason && $rejectionReason !== 'No reason provided')
                                <div class="reason-box">
                                    <div class="reason-label">Reason for Rejection</div>
                                    <div class="reason-text">{{ html_entity_decode($rejectionReason, ENT_QUOTES, 'UTF-8') }}</div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="divider"></div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                If you believe this is an error or have questions about this decision, please contact the administrator for further assistance.
                            </p>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 15px 0 0 0; line-height: 1.6;">
                                Thank you for your interest in the Board Members Portal.
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

