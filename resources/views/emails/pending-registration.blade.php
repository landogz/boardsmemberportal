<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Registration Pending Approval</title>
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
        
        .registration-card {
            background-color: #f9fafb;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .registration-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 15px 0;
            line-height: 1.4;
        }
        
        .registration-details {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 0;
        }
        
        .registration-details p {
            margin: 0 0 8px 0;
        }
        
        .registration-details p:last-child {
            margin-bottom: 0;
        }
        
        .detail-item {
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 0;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
        }
        
        .detail-value {
            font-size: 15px;
            color: #111827;
            font-weight: 500;
            line-height: 1.5;
            word-break: break-word;
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
            .registration-card { padding: 16px !important; }
            .registration-title { font-size: 16px !important; }
            .button { display: block !important; width: 100% !important; max-width: 100%; box-sizing: border-box; padding: 14px 16px !important; font-size: 16px !important; }
            .detail-item { padding: 12px 0 !important; }
            .detail-label { font-size: 10px !important; margin-bottom: 4px !important; }
            .detail-value { font-size: 14px !important; }
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
                            <h1>{{ ($forRegistrant ?? false) ? '📋 Registration Submitted' : '🔔 New Registration Pending Approval' }}</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            @if($forRegistrant ?? false)
                            {{-- Email for the registering user (pending approval notice) --}}
                            <p class="greeting">
                                Dear {{ trim(($registeredUser->pre_nominal_title ?? '') . ' ' . ucwords(strtolower(trim($registeredUser->first_name . ' ' . $registeredUser->last_name))) . ($registeredUser->extension_name ? ' ' . $registeredUser->extension_name : '')) }},
                            </p>
                            
                            <p class="greeting">
                                Thank you for registering with the Board Members Portal. Your registration has been successfully submitted and is pending administrative approval.
                            </p>
                            
                            <div class="registration-card">
                                <h2 class="registration-title">📋 What happens next?</h2>
                                <p class="registration-details">
                                    The Conference Secretariat of the Dangerous Drugs Board will review your registration. You will receive a separate notification once your account has been approved, after which you may log in using your registered credentials.
                                </p>
                            </div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                Should you have any questions or require further assistance, kindly contact the Conference Secretariat through email at boardsec@ddb.gov.ph.
                            </p>
                            @else
                            {{-- Email for admins (new registration alert) --}}
                            <p class="greeting">
                                Hello <strong>{{ $adminUser->short_name }}</strong>,
                            </p>
                            
                            <p class="greeting">
                                A new user registration has been submitted and requires your approval.
                            </p>
                            
                            <div class="registration-card">
                                <h2 class="registration-title">📋 Registration Details</h2>
                                <div class="registration-details">
                                    <div class="detail-item">
                                        <div class="detail-label">👤 Full Name</div>
                                        <div class="detail-value">{{ $registeredUser->full_name }}</div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">✉️ Email Address</div>
                                        <div class="detail-value">{{ $registeredUser->email }}</div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">🔑 Username</div>
                                        <div class="detail-value">{{ $registeredUser->username }}</div>
                                    </div>
                                    
                                    @if($registeredUser->governmentAgency)
                                    <div class="detail-item">
                                        <div class="detail-label">🏢 Government Agency</div>
                                        <div class="detail-value">{{ $registeredUser->governmentAgency->name }}</div>
                                    </div>
                                    @endif
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">🆔 Representative Type</div>
                                        <div class="detail-value">{{ $registeredUser->representative_type }}</div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">💼 Designation</div>
                                        <div class="detail-value">{{ $registeredUser->designation }}</div>
                                    </div>
                                    
                                    <div class="detail-item" style="border-bottom: none; padding-bottom: 0;">
                                        <div class="detail-label">🕐 Submitted</div>
                                        <div class="detail-value">{{ $registeredUser->created_at->format('F d, Y \a\t g:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="button-container">
                                <a href="{{ $pendingRegistrationsUrl }}" class="button" style="background-color: #055498; background: linear-gradient(135deg, #055498 0%, #123a60 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">Review Registration</a>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                This notification was sent to you by <strong>Board Members Portal</strong>.
                                Please review and approve or reject this registration request.
                            </p>
                            @endif
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

