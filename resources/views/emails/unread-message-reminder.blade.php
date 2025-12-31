<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unread Message Reminder</title>
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
        
        .message-card {
            background-color: #f9fafb;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .message-sender {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
        }
        
        .message-content {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 0;
            word-break: break-word;
        }
        
        .message-time {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
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
            .email-body {
                padding: 30px 20px;
            }
            
            .message-card {
                padding: 15px;
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
                            <h1>💬 Unread Message Reminder</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,
                            </p>
                            
                            <p class="greeting">
                                @if($isGroupMessage)
                                    You have an unread group message that has been waiting for your attention for over 24 hours.
                                @else
                                    You have an unread message that has been waiting for your attention for over 24 hours.
                                @endif
                            </p>
                            
                            <div class="message-card">
                                <div class="message-sender">
                                    @if($isGroupMessage)
                                        Group: {{ $chatMessage->group->name }}<br>
                                        From: {{ $sender->first_name }} {{ $sender->last_name }}
                                    @else
                                        From: {{ $sender->first_name }} {{ $sender->last_name }}
                                    @endif
                                </div>
                                <div class="message-content">
                                    {{ html_entity_decode(strip_tags($chatMessage->message), ENT_QUOTES, 'UTF-8') }}
                                </div>
                                <div class="message-time">
                                    Sent: {{ $chatMessage->timestamp->format('F d, Y \a\t g:i A') }}
                                </div>
                            </div>
                            
                            <div class="button-container">
                                <a href="{{ $messagesUrl }}" class="button" style="background-color: #055498; background: linear-gradient(135deg, #055498 0%, #123a60 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">View Messages</a>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                This is a friendly reminder to check your messages. If you have any questions, please contact the administrator.
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

