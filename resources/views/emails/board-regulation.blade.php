<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Board Regulation</title>
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
            max-width: 100%;
            height: auto !important;
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
        
        .regulation-card {
            background-color: #f9fafb;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .regulation-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 15px 0;
            line-height: 1.4;
        }
        
        .regulation-details {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 15px 0;
        }
        
        .regulation-details strong {
            color: #111827;
        }
        
        .regulation-description {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 15px 0 0 0;
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #055498;
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
            background-color: #123a60;
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
            .regulation-card { padding: 16px !important; }
            .regulation-title { font-size: 18px !important; }
            .button { display: block !important; width: 100% !important; max-width: 100%; box-sizing: border-box; padding: 14px 16px !important; font-size: 16px !important; }
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-wrapper" style="max-width: 100%; width: 100%;">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                            <h1>📋 New Board Regulation</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Dear {{ trim(($user->pre_nominal_title ?? '') . ' ' . ucwords(strtolower(trim($user->first_name . ' ' . $user->last_name))) . ($user->extension_name ? ' ' . $user->extension_name : '')) }},
                            </p>
                            
                            <p class="greeting">
                                A new Board Regulation has been published and is now available for your viewing in the Board Members Portal.
                            </p>
                            
                            <div class="regulation-card">
                                <h2 class="regulation-title">{{ $regulation->title }}</h2>
                                
                                @if($regulation->version)
                                <div class="regulation-details">
                                    <strong>Version:</strong> {{ $regulation->version }}
                                </div>
                                @endif
                                
                                @if($regulation->effective_date)
                                <div class="regulation-details">
                                    <strong>Effective Date:</strong> {{ $regulation->effective_date->format('F d, Y') }}
                                </div>
                                @endif
                                
                                @if($regulation->approved_date)
                                <div class="regulation-details">
                                    <strong>Approved Date:</strong> {{ $regulation->approved_date->format('F d, Y') }}
                                </div>
                                @endif
                                
                                @if($regulation->description)
                                <div class="regulation-description">
                                    {{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($regulation->description), ENT_QUOTES, 'UTF-8'), 300) }}
                                </div>
                                @endif
                            </div>
                            
                            <div class="button-container">
                                <a href="{{ $regulationUrl }}" class="button" style="background-color: #055498; background: linear-gradient(135deg, #055498 0%, #123a60 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">Sign In to View</a>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                This notice was sent through the <strong>Board Members Portal</strong>.
                                Should you have any questions or require further assistance, kindly contact the Conference Secretariat through email at boardsec@ddb.gov.ph.
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

