<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Declined</title>
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
        
        .decline-card {
            background-color: #fef2f2;
            border-left: 4px solid #EF4444;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .decline-title {
            font-size: 18px;
            font-weight: 600;
            color: #991b1b;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        
        .decline-message {
            font-size: 14px;
            color: #b91c1c;
            line-height: 1.7;
            margin: 0;
        }
        
        .notice-info {
            background-color: #f9fafb;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .notice-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 15px 0;
            line-height: 1.4;
        }
        
        .notice-details {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 10px 0;
        }
        
        .user-info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .user-info strong {
            color: #1e40af;
            display: block;
            margin-bottom: 8px;
        }
        
        .user-name {
            color: #1e3a8a;
            font-weight: 600;
        }
        
        .reason-box {
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .reason-box strong {
            color: #9a3412;
            display: block;
            margin-bottom: 8px;
        }
        
        .reason-text {
            color: #7c2d12;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #055498;
            background: linear-gradient(135deg, #055498 0%, #0d4a7a 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            line-height: 1.5;
            text-align: center;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .button:hover {
            background: linear-gradient(135deg, #0d4a7a 0%, #055498 100%);
            box-shadow: 0 4px 8px rgba(5, 84, 152, 0.3);
            transform: translateY(-1px);
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
            .email-body {
                padding: 30px 20px;
            }
            
            .decline-card, .notice-info {
                padding: 15px;
            }
            
            .notice-title {
                font-size: 16px;
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
                            <h1>✗ Notice Declined</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="greeting">Hello {{ $creator->first_name }},</p>
                            
                            <div class="decline-card">
                                <div class="decline-title">✗ Invitation Declined</div>
                                <p class="decline-message">
                                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong> has declined the invitation to attend the notice: <strong>"{{ $notice->title }}"</strong>.
                                </p>
                            </div>
                            
                            <div class="notice-info">
                                <div class="notice-title">{{ $notice->title }}</div>
                                <div class="notice-details">
                                    @if($notice->meeting_date)
                                        <strong>Meeting Date:</strong> {{ \Carbon\Carbon::parse($notice->meeting_date)->format('F d, Y') }}<br>
                                    @endif
                                    @if($notice->meeting_time)
                                        <strong>Meeting Time:</strong> {{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}<br>
                                    @endif
                                    <strong>Notice Type:</strong> {{ $notice->notice_type }}<br>
                                    <strong>Meeting Type:</strong> {{ ucfirst($notice->meeting_type) }}
                                </div>
                            </div>
                            
                            <div class="user-info">
                                <strong>Declined By:</strong>
                                <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span><br>
                                <span style="color: #4b5563; font-size: 13px;">{{ $user->email }}</span>
                                @if($user->governmentAgency)
                                    <br><span style="color: #6b7280; font-size: 13px;">{{ $user->governmentAgency->name }}</span>
                                @endif
                            </div>
                            
                            <div class="reason-box">
                                <strong>Reason for Declining:</strong>
                                <div class="reason-text">{{ $declinedReason }}</div>
                            </div>
                            
                            <div class="button-container">
                                <a href="{{ $noticeUrl }}" class="button">View Notice</a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p class="footer-text">
                                This email was sent to notify you that a user has declined the notice invitation.<br>
                                If you have any questions, please contact the administrator.
                            </p>
                            <p class="footer-text" style="margin-top: 15px;">
                                © {{ date('Y') }} Board Members Portal. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

