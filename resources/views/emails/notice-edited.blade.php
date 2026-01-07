<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Notice</title>
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
        
        .notice-card {
            background-color: #f9fafb;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .notice-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 15px 0;
            line-height: 1.4;
        }
        
        .notice-details {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 15px 0;
        }
        
        .notice-detail-row {
            margin: 10px 0;
        }
        
        .notice-detail-label {
            font-weight: 600;
            color: #374151;
            display: inline-block;
            min-width: 120px;
        }
        
        .notice-detail-value {
            color: #4b5563;
        }
        
        .notice-content {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.7;
            margin: 15px 0;
        }
        
        .notice-content p {
            margin: 0 0 12px 0;
        }
        
        .notice-content p:last-child {
            margin-bottom: 0;
        }
        
        .meeting-link-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .meeting-link-box strong {
            color: #1e40af;
            display: block;
            margin-bottom: 8px;
        }
        
        .meeting-link {
            color: #2563eb;
            word-break: break-all;
            text-decoration: none;
        }
        
        .meeting-link:hover {
            text-decoration: underline;
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
        
        .footer-link {
            color: #055498;
            text-decoration: none;
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
            
            .notice-card {
                padding: 15px;
            }
            
            .notice-title {
                font-size: 18px;
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
                            <h1>📋 New Notice</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,
                            </p>
                            
                            <p class="greeting">
                                A notice you have access to has been updated. Please review the changes below.
                            </p>
                            
                            <div class="notice-card">
                                <h2 class="notice-title">{{ $notice->title }}</h2>
                                
                                <div class="notice-details">
                                    <div class="notice-detail-row">
                                        <span class="notice-detail-label">Notice Type:</span>
                                        <span class="notice-detail-value">{{ $notice->notice_type }}</span>
                                    </div>
                                    <div class="notice-detail-row">
                                        <span class="notice-detail-label">Meeting Type:</span>
                                        <span class="notice-detail-value">{{ ucfirst($notice->meeting_type) }}</span>
                                    </div>
                                    @if($notice->meeting_date)
                                    <div class="notice-detail-row">
                                        <span class="notice-detail-label">Meeting Date:</span>
                                        <span class="notice-detail-value">{{ \Carbon\Carbon::parse($notice->meeting_date)->format('F d, Y') }}</span>
                                    </div>
                                    @endif
                                    @if($notice->meeting_time)
                                    <div class="notice-detail-row">
                                        <span class="notice-detail-label">Meeting Time:</span>
                                        <span class="notice-detail-value">{{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}</span>
                                    </div>
                                    @endif
                                    @if($notice->notice_type === 'Board Issuances' && $notice->no_of_attendees)
                                    <div class="notice-detail-row">
                                        <span class="notice-detail-label">No. of Attendees:</span>
                                        <span class="notice-detail-value">{{ $notice->no_of_attendees }}</span>
                                    </div>
                                    @endif
                                    @if($notice->notice_type === 'Board Issuances')
                                        @php
                                            $selectedRegulations = $notice->board_regulations ?? [];
                                            if (is_string($selectedRegulations)) {
                                                $selectedRegulations = json_decode($selectedRegulations, true) ?? [];
                                            }
                                            $selectedResolutions = $notice->board_resolutions ?? [];
                                            if (is_string($selectedResolutions)) {
                                                $selectedResolutions = json_decode($selectedResolutions, true) ?? [];
                                            }
                                            $regulations = !empty($selectedRegulations) ? \App\Models\BoardRegulation::whereIn('id', $selectedRegulations)->get() : collect([]);
                                            $resolutions = !empty($selectedResolutions) ? \App\Models\OfficialDocument::whereIn('id', $selectedResolutions)->get() : collect([]);
                                        @endphp
                                        @if($regulations->count() > 0)
                                        <div class="notice-detail-row" style="margin-top: 15px;">
                                            <span class="notice-detail-label" style="display: block; margin-bottom: 8px;">Board Regulations:</span>
                                            <div style="margin-left: 0;">
                                                @foreach($regulations as $regulation)
                                                <div style="margin-bottom: 6px; padding: 8px; background-color: #f3f4f6; border-radius: 4px;">
                                                    <strong style="color: #374151;">{{ $regulation->title }}</strong>
                                                    @if($regulation->effective_date)
                                                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                                                        Effective: {{ $regulation->effective_date->format('F d, Y') }}
                                                    </div>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        @if($resolutions->count() > 0)
                                        <div class="notice-detail-row" style="margin-top: 15px;">
                                            <span class="notice-detail-label" style="display: block; margin-bottom: 8px;">Board Resolutions:</span>
                                            <div style="margin-left: 0;">
                                                @foreach($resolutions as $resolution)
                                                <div style="margin-bottom: 6px; padding: 8px; background-color: #f3f4f6; border-radius: 4px;">
                                                    <strong style="color: #374151;">{{ $resolution->title }}</strong>
                                                    @if($resolution->effective_date)
                                                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                                                        Effective: {{ $resolution->effective_date->format('F d, Y') }}
                                                    </div>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                    @if($notice->description)
                                    <div class="notice-detail-row">
                                        <span class="notice-detail-label">Description:</span>
                                    </div>
                                    <div class="notice-content">
                                        {{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($notice->description), ENT_QUOTES, 'UTF-8'), 500) }}
                                    </div>
                                    @endif
                                    @if(in_array($notice->meeting_type, ['online', 'hybrid']) && $notice->meeting_link)
                                    <div class="meeting-link-box">
                                        <strong>🔗 Meeting Link:</strong>
                                        <a href="{{ $notice->meeting_link }}" class="meeting-link" target="_blank">{{ $notice->meeting_link }}</a>
                                    </div>
                                    @endif
                                    @if($notice->attachments && count($notice->attachments) > 0)
                                    <div class="notice-detail-row" style="margin-top: 20px;">
                                        <span class="notice-detail-label">Attachments:</span>
                                    </div>
                                    <div style="margin-top: 10px;">
                                        @php
                                            $attachmentMedia = $notice->attachmentMedia;
                                        @endphp
                                        @foreach($attachmentMedia as $media)
                                        <div style="margin-bottom: 8px; padding: 10px; background-color: #f3f4f6; border-radius: 4px;">
                                            <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank" style="color: #2563eb; text-decoration: none; display: inline-flex; align-items: center;">
                                                <span style="margin-right: 8px;">📎</span>
                                                <span>{{ $media->file_name }}</span>
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="button-container">
                                <a href="{{ $noticeUrl }}" class="button" style="background-color: #055498; background: linear-gradient(135deg, #055498 0%, #123a60 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">View Notice</a>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                This notice was sent to you by <strong>Board Members Portal</strong>.
                                If you have any questions, please contact the administrator.
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

