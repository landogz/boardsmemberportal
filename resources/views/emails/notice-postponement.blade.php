<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice of Postponement</title>
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
        .notice-card {
            background-color: #f9fafb;
            border-left: 4px solid #055498;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
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
            .notice-card { padding: 16px !important; }
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
                            <h1>Notice of Postponement</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <p class="greeting">
                                Dear {{ $user->pre_nominal_title ?? '' }} {{ $user->last_name }},
                            </p>
                            @php
                                $related = $notice->relatedNotice;
                                $venueText = 'Board Room, Dangerous Drug Board Extension Office, 6th Floor, South Insula Building, 61 Timog Avenue, Quezon City';
                                if ($related && !empty($related->venue)) {
                                    $venueText = $related->venue;
                                }
                                $titleText = $related ? $related->title : $notice->title;
                                $dateDay = $related && $related->meeting_date
                                    ? \Carbon\Carbon::parse($related->meeting_date)->format('l, F j, Y')
                                    : '';
                                $timeText = $related && $related->meeting_time
                                    ? \Carbon\Carbon::parse($related->meeting_time)->format('g:i A')
                                    : '';
                                $reasons = $notice->description
                                    ? trim(html_entity_decode(strip_tags($notice->description), ENT_QUOTES, 'UTF-8'))
                                    : '';
                            @endphp
                            <div class="notice-card">
                                <p class="notice-content">
                                    Please be informed that the <strong>{{ $titleText }}</strong>, originally scheduled for {{ $dateDay }}{{ $dateDay && $timeText ? ', ' : '' }}{{ $timeText }}{{ ($dateDay || $timeText) ? ' at ' : '' }}{{ $venueText }}, is hereby postponed{{ $reasons ? ' ' . rtrim($reasons, '. ') . '.' : '.' }}
                                </p>
                                @if($notice->attachments && count($notice->attachments) > 0)
                                <div style="margin-top: 16px;">
                                    <strong style="color: #374151;">Attachments:</strong>
                                    @php $attachmentMedia = $notice->attachmentMedia; @endphp
                                    @foreach($attachmentMedia as $media)
                                    <div style="margin: 8px 0;">
                                        <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank" style="color: #2563eb; text-decoration: none;">📎 {{ $media->file_name }}</a>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <p class="notice-content">
                                Thank you for your usual support on matters of mutual concern.
                            </p>
                            <div class="divider"></div>
                            <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6;">
                                This notice was sent through the <strong>Board Members Portal</strong>.
                                For any inquiries or clarification, please coordinate with the system administrator.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p class="footer-text">© {{ date('Y') }} Board Members Portal. All rights reserved.</p>
                            <p class="footer-text" style="margin-top: 8px;">This is an automated email. Please do not reply to this message.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
