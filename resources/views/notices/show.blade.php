<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $notice->title }} - Notices</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('components.header-footer-styles')
    <style>
        .notice-detail-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .notice-header-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .dark .notice-header-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .notice-header-content {
            padding: 2rem;
        }
        
        .notice-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }
        
        .badge-meeting {
            background: linear-gradient(135deg, rgba(5, 84, 152, 0.1) 0%, rgba(5, 84, 152, 0.15) 100%);
            color: #055498;
            border: 1px solid rgba(5, 84, 152, 0.2);
        }
        
        .badge-agenda {
            background: linear-gradient(135deg, rgba(206, 32, 40, 0.1) 0%, rgba(206, 32, 40, 0.15) 100%);
            color: #CE2028;
            border: 1px solid rgba(206, 32, 40, 0.2);
        }
        
        .badge-other {
            background: linear-gradient(135deg, rgba(100, 116, 139, 0.1) 0%, rgba(100, 116, 139, 0.15) 100%);
            color: #64748b;
            border: 1px solid rgba(100, 116, 139, 0.2);
        }
        
        .notice-title {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .dark .notice-title {
            color: #f1f5f9;
        }
        
        .notice-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .dark .notice-meta-grid {
            border-top-color: #334155;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }
        
        .meta-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #475569;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .meta-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        
        .dark .meta-icon {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
            color: #cbd5e1;
        }
        
        .dark .meta-icon img {
            border: 2px solid #334155;
        }
        
        .meta-content {
            flex: 1;
        }
        
        .meta-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }
        
        .dark .meta-label {
            color: #94a3b8;
        }
        
        .meta-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
        }
        
        .dark .meta-value {
            color: #f1f5f9;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        
        .status-accepted {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .status-declined {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .status-pending {
            background: rgba(251, 191, 36, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(251, 191, 36, 0.2);
        }
        
        .notice-content-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .dark .notice-content-card {
            background: #1e293b;
            border-color: #334155;
        }
        
        .content-section {
            padding: 2rem;
        }
        
        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .dark .section-title {
            color: #f1f5f9;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 1.5rem;
            background: linear-gradient(135deg, #055498 0%, #0ea5e9 100%);
            border-radius: 2px;
        }
        
        .notice-description {
            text-indent: 0 !important;
            text-align: left !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
            font-size: 1rem;
            line-height: 1.75;
            color: #475569;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .notice-description::first-line {
            text-indent: 0 !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
        
        .notice-description * {
            text-indent: 0 !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
            text-align: left !important;
        }
        
        .notice-description p:first-child,
        .notice-description div:first-child,
        .notice-description span:first-child {
            text-indent: 0 !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
        
        .dark .notice-description {
            color: #cbd5e1;
        }
        
        .details-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .detail-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
        }
        
        .dark .detail-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .detail-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        
        .dark .detail-label {
            color: #94a3b8;
        }
        
        .detail-value {
            font-size: 0.9375rem;
            font-weight: 600;
            color: #0f172a;
        }
        
        .dark .detail-value {
            color: #f1f5f9;
        }
        
        .detail-link {
            color: #055498;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .detail-link:hover {
            color: #0ea5e9;
            text-decoration: underline;
        }
        
        .attachments-section {
            margin-top: 2rem;
        }
        
        .attachments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .attachment-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .dark .attachment-card {
            background: #1e293b;
            border-color: #334155;
        }
        
        .attachment-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        
        .attachment-preview {
            width: 100%;
            height: 120px;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .attachment-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .attachment-preview.pdf {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }
        
        .attachment-preview.file {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        }
        
        .attachment-name {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }
        
        .dark .attachment-name {
            color: #f1f5f9;
        }
        
        .attachment-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #055498;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .attachment-action:hover {
            color: #0ea5e9;
        }
        
        .actions-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        
        .dark .actions-card {
            background: #1e293b;
            border-color: #334155;
        }
        
        .actions-content {
            padding: 2rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .btn-action {
            flex: 1;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-accept {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }
        
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -1px rgba(16, 185, 129, 0.4);
        }
        
        .btn-decline {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }
        
        .btn-decline:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -1px rgba(239, 68, 68, 0.4);
        }
        
        .btn-agenda {
            background: linear-gradient(135deg, #055498 0%, #0ea5e9 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(5, 84, 152, 0.3);
        }
        
        .btn-agenda:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -1px rgba(5, 84, 152, 0.4);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }
        
        .back-link:hover {
            color: #055498;
        }
        
        .dark .back-link {
            color: #94a3b8;
        }
        
        .dark .back-link:hover {
            color: #0ea5e9;
        }
        
        @media (max-width: 768px) {
            .notice-detail-container {
                padding: 1rem;
            }
            
            .notice-header-content,
            .content-section,
            .actions-content {
                padding: 1.5rem;
            }
            
            .notice-title {
                font-size: 1.5rem;
            }
            
            .notice-meta-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .attachments-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    @include('components.header')
    
    <div class="notice-detail-container">
        <a href="{{ route('notices.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Notices</span>
        </a>

        <!-- Notice Header Card -->
        <div class="notice-header-card">
            <div class="notice-header-content">
                @php
                    $typeClass = 'badge-other';
                    if ($notice->notice_type === 'Notice of Meeting') {
                        $typeClass = 'badge-meeting';
                    } elseif ($notice->notice_type === 'Agenda') {
                        $typeClass = 'badge-agenda';
                    }
                @endphp
                <span class="notice-badge {{ $typeClass }}">
                    <i class="fas fa-file-alt"></i>
                    {{ $notice->notice_type }}
                </span>
                
                <h1 class="notice-title">{{ $notice->title }}</h1>
                
                @if($attendanceConfirmation)
                    <div class="status-indicator status-{{ $attendanceConfirmation->status }}">
                        @if($attendanceConfirmation->status === 'accepted')
                            <i class="fas fa-check-circle"></i>
                            <span>You have accepted this invitation</span>
                        @elseif($attendanceConfirmation->status === 'declined')
                            <i class="fas fa-times-circle"></i>
                            <span>You have declined this invitation</span>
                        @else
                            <i class="fas fa-clock"></i>
                            <span>Response pending</span>
                        @endif
                    </div>
                @endif
                
                <div class="notice-meta-grid">
                    <div class="meta-item">
                        <div class="meta-icon" style="background: transparent; padding: 0;">
                            @php
                                $creatorProfilePic = 'https://ui-avatars.com/api/?name=' . urlencode($notice->creator->first_name . ' ' . $notice->creator->last_name) . '&size=48&background=055498&color=fff&bold=true';
                                if ($notice->creator->profile_picture) {
                                    $media = \App\Models\MediaLibrary::find($notice->creator->profile_picture);
                                    if ($media) {
                                        $creatorProfilePic = asset('storage/' . $media->file_path);
                                    }
                                }
                            @endphp
                            <img src="{{ $creatorProfilePic }}" alt="{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem;">
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Created By</div>
                            <div class="meta-value">{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}</div>
                        </div>
                    </div>
                    
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Date Created</div>
                            <div class="meta-value">{{ $notice->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    
                    @if($notice->meeting_date)
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Meeting Date</div>
                                <div class="meta-value">{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if($notice->meeting_time)
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Meeting Time</div>
                                <div class="meta-value">{{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notice Content Card -->
        <div class="notice-content-card">
            <div class="content-section">
                @if($notice->description)
                    <h2 class="section-title">Description</h2>
                    <div class="notice-description">
                        {!! nl2br(e(strip_tags(trim($notice->description)))) !!}
                    </div>
                @endif
                
                <div class="details-grid">
                    <div class="detail-card">
                        <div class="detail-label">Meeting Type</div>
                        <div class="detail-value">{{ ucfirst($notice->meeting_type) }}</div>
                    </div>
                    
                    @if(in_array($notice->meeting_type, ['online', 'hybrid']) && $notice->meeting_link)
                        <div class="detail-card">
                            <div class="detail-label">Meeting Link</div>
                            <div class="detail-value">
                                @php
                                    $meetingUrl = strtolower($notice->meeting_link);
                                    $platform = 'default';
                                    $platformName = 'Meeting';
                                    $platformIcon = 'fa-video';
                                    $platformColor = '#055498';
                                    
                                    if (strpos($meetingUrl, 'zoom.us') !== false || strpos($meetingUrl, 'zoom.com') !== false) {
                                        $platform = 'zoom';
                                        $platformName = 'Zoom';
                                        $platformIcon = 'fa-video';
                                        $platformColor = '#2D8CFF';
                                    } elseif (strpos($meetingUrl, 'meet.google.com') !== false || strpos($meetingUrl, 'google.com/meet') !== false) {
                                        $platform = 'google-meet';
                                        $platformName = 'Google Meet';
                                        $platformIcon = 'fa-video';
                                        $platformColor = '#00832D';
                                    } elseif (strpos($meetingUrl, 'teams.microsoft.com') !== false || strpos($meetingUrl, 'teams.live.com') !== false) {
                                        $platform = 'teams';
                                        $platformName = 'Microsoft Teams';
                                        $platformIcon = 'fa-video';
                                        $platformColor = '#6264A7';
                                    } elseif (strpos($meetingUrl, 'webex.com') !== false) {
                                        $platform = 'webex';
                                        $platformName = 'Webex';
                                        $platformIcon = 'fa-video';
                                        $platformColor = '#00AEEF';
                                    } elseif (strpos($meetingUrl, 'gotomeeting.com') !== false) {
                                        $platform = 'gotomeeting';
                                        $platformName = 'GoToMeeting';
                                        $platformIcon = 'fa-video';
                                        $platformColor = '#F68D2E';
                                    }
                                @endphp
                                <a href="{{ $notice->meeting_link }}" target="_blank" class="detail-link inline-flex items-center gap-2 px-3 py-2 rounded-lg transition-colors hover:opacity-90 font-medium" style="color: {{ $platformColor }}; background: {{ $platformColor }}15; border: 1px solid {{ $platformColor }}40;">
                                    @if($platform === 'zoom')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 5.605L12 11.979 8.075 13.826l-1.97-5.605L12 6.275l5.894 1.946z"/>
                                        </svg>
                                    @elseif($platform === 'google-meet')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19.53 9.75L15.75 6H8.25L4.47 9.75a.75.75 0 0 0-.22.53v3.44c0 .2.08.39.22.53L8.25 18h7.5l3.78-3.75a.75.75 0 0 0 .22-.53v-3.44a.75.75 0 0 0-.22-.53zM12 13.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                        </svg>
                                    @elseif($platform === 'teams')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19.5 4.5c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2s2-.897 2-2v-11c0-1.103-.897-2-2-2zm-15 0c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2s2-.897 2-2v-11c0-1.103-.897-2-2-2zm7.5 0c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2s2-.897 2-2v-11c0-1.103-.897-2-2-2z"/>
                                        </svg>
                                    @else
                                        <i class="fas {{ $platformIcon }}"></i>
                                    @endif
                                    <span>Join {{ $platformName }}</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    @if($notice->notice_type === 'Agenda' && $notice->no_of_attendees)
                        <div class="detail-card">
                            <div class="detail-label">No. of Attendees</div>
                            <div class="detail-value">{{ $notice->no_of_attendees }}</div>
                        </div>
                    @endif
                    
                    @if($notice->notice_type === 'Agenda' && $notice->relatedNotice)
                        <div class="detail-card">
                            <div class="detail-label">Related Notice</div>
                            <div class="detail-value">{{ $notice->relatedNotice->title }}</div>
                        </div>
                    @endif
                </div>

                @if($notice->attachments && count($notice->attachments) > 0)
                    <div class="attachments-section">
                        <h2 class="section-title">Attachments</h2>
                        <div class="attachments-grid">
                            @foreach($notice->attachment_media as $attachment)
                                @php
                                    $isImage = in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $isPdf = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) === 'pdf';
                                @endphp
                                <div class="attachment-card">
                                    @if($isImage)
                                        <div class="attachment-preview">
                                            <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}">
                                        </div>
                                    @elseif($isPdf)
                                        <div class="attachment-preview pdf" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')">
                                            <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                                        </div>
                                    @else
                                        <div class="attachment-preview file">
                                            <i class="fas fa-file text-5xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="attachment-name" title="{{ $attachment->file_name }}">
                                        {{ $attachment->file_name }}
                                    </div>
                                    @if($isPdf)
                                        <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ addslashes($attachment->file_name) }}')" class="attachment-action">
                                            <i class="fas fa-eye"></i>
                                            <span>View PDF</span>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.media-library.download', $attachment->id) }}" target="_blank" class="attachment-action">
                                            <i class="fas fa-download"></i>
                                            <span>Download</span>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions Card -->
        <div class="actions-card">
            <div class="actions-content">
                @if(!$attendanceConfirmation || $attendanceConfirmation->status === 'pending')
                    <div class="action-buttons">
                        <button class="btn-action btn-accept" onclick="acceptNotice({{ $notice->id }})">
                            <i class="fas fa-check"></i>
                            <span>Accept Invitation</span>
                        </button>
                        <button class="btn-action btn-decline" onclick="declineNotice({{ $notice->id }})">
                            <i class="fas fa-times"></i>
                            <span>Decline Invitation</span>
                        </button>
                    </div>
                @elseif($attendanceConfirmation->status === 'accepted' && !$isMeetingDone && !$agendaRequest)
                    <div class="action-buttons">
                        <button class="btn-action btn-agenda" onclick="requestAgendaInclusion({{ $notice->id }})">
                            <i class="fas fa-plus"></i>
                            <span>Request Agenda Inclusion</span>
                        </button>
                    </div>
                @elseif($attendanceConfirmation->status === 'accepted' && !$isMeetingDone && $agendaRequest)
                    <div class="text-center">
                        <div class="status-indicator status-{{ $agendaRequest->status === 'approved' ? 'accepted' : ($agendaRequest->status === 'rejected' ? 'declined' : 'pending') }}">
                            @if($agendaRequest->status === 'approved')
                                <i class="fas fa-check-circle"></i>
                                <span>Agenda Request: Approved</span>
                            @elseif($agendaRequest->status === 'rejected')
                                <i class="fas fa-times-circle"></i>
                                <span>Agenda Request: Rejected</span>
                            @else
                                <i class="fas fa-clock"></i>
                                <span>Agenda Request: Pending Review</span>
                            @endif
                        </div>
                    </div>
                @elseif($attendanceConfirmation->status === 'accepted' && $isMeetingDone && !$referenceMaterial)
                    <div class="action-buttons">
                        <button class="btn-action btn-agenda" onclick="submitReferenceMaterial({{ $notice->id }})">
                            <i class="fas fa-file-upload"></i>
                            <span>Submit Reference Materials</span>
                        </button>
                    </div>
                @elseif($attendanceConfirmation->status === 'accepted' && $isMeetingDone && $referenceMaterial)
                    <div class="text-center">
                        <div class="status-indicator status-{{ $referenceMaterial->status === 'approved' ? 'accepted' : ($referenceMaterial->status === 'rejected' ? 'declined' : 'pending') }}">
                            @if($referenceMaterial->status === 'approved')
                                <i class="fas fa-check-circle"></i>
                                <span>Reference Materials: Approved</span>
                            @elseif($referenceMaterial->status === 'rejected')
                                <i class="fas fa-times-circle"></i>
                                <span>Reference Materials: Rejected</span>
                            @else
                                <i class="fas fa-clock"></i>
                                <span>Reference Materials: Pending Review</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('components.footer')
    
    <!-- Global PDF Modal -->
    @include('components.pdf-modal')

    <!-- Decline Reason Modal -->
    <div id="declineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Decline Invitation</h3>
            <form id="declineForm">
                <input type="hidden" id="declineNoticeId" name="notice_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for declining <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="declineReason" 
                        name="reason" 
                        rows="4" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none dark:bg-gray-700 dark:text-white"
                        placeholder="Please provide a reason for declining this invitation..."
                    ></textarea>
                </div>
                <div class="flex gap-3">
                    <button 
                        type="button" 
                        onclick="closeDeclineModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Agenda Inclusion Request Modal -->
    <div id="agendaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Request for Agenda Inclusion</h3>
            <form id="agendaForm">
                <input type="hidden" id="agendaNoticeId" name="notice_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="agendaDescription" 
                        name="description" 
                        rows="6" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none dark:bg-gray-700 dark:text-white"
                        placeholder="Describe what you would like to include in the agenda..."
                    ></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Attachments
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <input 
                            type="file" 
                            id="agendaAttachments" 
                            name="attachments[]" 
                            multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif"
                            class="hidden"
                        >
                        <div id="agendaDropZone" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                <span class="text-[#055498] font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF (Max: 30MB per file)</p>
                        </div>
                        <div id="agendaAttachmentsPreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button 
                        type="button" 
                        onclick="closeAgendaModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors"
                    >
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reference Materials Submission Modal -->
    <div id="referenceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Submit Reference Materials</h3>
            <form id="referenceForm">
                <input type="hidden" id="referenceNoticeId" name="notice_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="referenceDescription" 
                        name="description" 
                        rows="6" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none dark:bg-gray-700 dark:text-white"
                        placeholder="Describe the reference materials you are submitting..."
                    ></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Attachments
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <input 
                            type="file" 
                            id="referenceAttachments" 
                            name="attachments[]" 
                            multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif"
                            class="hidden"
                        >
                        <div id="referenceDropZone" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                <span class="text-[#055498] font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF (Max: 30MB per file)</p>
                        </div>
                        <div id="referenceAttachmentsPreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button 
                        type="button" 
                        onclick="closeReferenceModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors"
                    >
                        Submit Materials
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        let uploadedAttachmentIds = [];
        let uploadedReferenceAttachmentIds = [];
        let currentNoticeId = null;

        // Auto-trigger action from email link
        @if(isset($autoAction) && $autoAction === 'accept')
            $(document).ready(function() {
                setTimeout(function() {
                    acceptNotice({{ $notice->id }});
                }, 500);
            });
        @elseif(isset($autoAction) && $autoAction === 'decline')
            $(document).ready(function() {
                setTimeout(function() {
                    declineNotice({{ $notice->id }});
                }, 500);
            });
        @endif

        function acceptNotice(noticeId) {
            Swal.fire({
                title: 'Accept Invitation?',
                text: 'Are you sure you want to accept this meeting invitation?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, accept',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`/notices/${noticeId}/accept`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.response?.data?.message || 'Failed to accept invitation.',
                            });
                        });
                }
            });
        }

        function declineNotice(noticeId) {
            currentNoticeId = noticeId;
            document.getElementById('declineNoticeId').value = noticeId;
            document.getElementById('declineReason').value = '';
            document.getElementById('declineModal').classList.remove('hidden');
        }

        function closeDeclineModal() {
            document.getElementById('declineModal').classList.add('hidden');
            document.getElementById('declineForm').reset();
        }

        function requestAgendaInclusion(noticeId) {
            currentNoticeId = noticeId;
            document.getElementById('agendaNoticeId').value = noticeId;
            document.getElementById('agendaDescription').value = '';
            uploadedAttachmentIds = [];
            document.getElementById('agendaAttachmentsPreview').innerHTML = '';
            document.getElementById('agendaModal').classList.remove('hidden');
        }

        function closeAgendaModal() {
            document.getElementById('agendaModal').classList.add('hidden');
            document.getElementById('agendaForm').reset();
            uploadedAttachmentIds = [];
            document.getElementById('agendaAttachmentsPreview').innerHTML = '';
        }

        // Decline form submission
        document.getElementById('declineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const noticeId = document.getElementById('declineNoticeId').value;
            const reason = document.getElementById('declineReason').value;
            
            axios.post(`/notices/${noticeId}/decline`, { reason })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            closeDeclineModal();
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.response?.data?.message || 'Failed to decline invitation.',
                    });
                });
        });

        // Agenda form submission
        document.getElementById('agendaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const noticeId = document.getElementById('agendaNoticeId').value;
            const description = document.getElementById('agendaDescription').value;
            
            const formData = new FormData();
            formData.append('description', description);
            if (uploadedAttachmentIds.length > 0) {
                uploadedAttachmentIds.forEach(id => {
                    formData.append('attachments[]', id);
                });
            }
            
            try {
                const response = await axios.post(`/notices/${noticeId}/agenda-inclusion`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        closeAgendaModal();
                        location.reload();
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.response?.data?.message || 'Failed to submit agenda inclusion request.',
                });
            }
        });

        // File upload handling for agenda attachments
        const agendaAttachmentsInput = document.getElementById('agendaAttachments');
        const agendaDropZone = document.getElementById('agendaDropZone');
        const agendaAttachmentsPreview = document.getElementById('agendaAttachmentsPreview');

        agendaDropZone.addEventListener('click', () => {
            agendaAttachmentsInput.click();
        });

        agendaAttachmentsInput.addEventListener('change', async function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                await handleAgendaFilesUpload(files);
                agendaAttachmentsInput.value = '';
            }
        });

        async function handleAgendaFilesUpload(files) {
            for (const file of files) {
                if (file.size > 30 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: `File "${file.name}" exceeds 30MB limit.`,
                    });
                    return;
                }
            }

            const uploadFormData = new FormData();
            files.forEach(file => {
                uploadFormData.append('files[]', file);
            });

            try {
                const uploadResponse = await axios.post('{{ route("admin.media-library.store") }}', uploadFormData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (uploadResponse.data.success && uploadResponse.data.files) {
                    const newIds = uploadResponse.data.files.map(file => file.id);
                    uploadedAttachmentIds = [...uploadedAttachmentIds, ...newIds];
                    
                    uploadResponse.data.files.forEach(file => {
                        const isImage = file.type.startsWith('image/');
                        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                        const previewHtml = `
                            <div class="relative border rounded-lg p-2 attachment-item" data-file-id="${file.id}">
                                <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors delete-agenda-attachment-btn" data-file-id="${file.id}">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                ${isImage ? 
                                    `<img src="${file.url}" alt="${file.name}" class="w-full h-24 object-cover rounded">` :
                                    isPdf ?
                                    `<div class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded">
                                        <i class="fas fa-file-pdf text-3xl text-red-500 mb-1"></i>
                                    </div>` :
                                    `<div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
                                        <i class="fas fa-file text-3xl text-gray-400"></i>
                                    </div>`
                                }
                                <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                            </div>
                        `;
                        agendaAttachmentsPreview.insertAdjacentHTML('beforeend', previewHtml);
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Error',
                    text: error.response?.data?.message || 'Failed to upload files.',
                });
            }
        }

        $(document).on('click', '.delete-agenda-attachment-btn', function(e) {
            e.preventDefault();
            const fileId = $(this).data('file-id');
            uploadedAttachmentIds = uploadedAttachmentIds.filter(id => id !== fileId);
            $(this).closest('.attachment-item').remove();
        });

        // Reference Materials functions
        function submitReferenceMaterial(noticeId) {
            currentNoticeId = noticeId;
            document.getElementById('referenceNoticeId').value = noticeId;
            document.getElementById('referenceDescription').value = '';
            uploadedReferenceAttachmentIds = [];
            document.getElementById('referenceAttachmentsPreview').innerHTML = '';
            document.getElementById('referenceModal').classList.remove('hidden');
        }

        function closeReferenceModal() {
            document.getElementById('referenceModal').classList.add('hidden');
            document.getElementById('referenceForm').reset();
            uploadedReferenceAttachmentIds = [];
            document.getElementById('referenceAttachmentsPreview').innerHTML = '';
        }

        // Reference form submission
        document.getElementById('referenceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const noticeId = document.getElementById('referenceNoticeId').value;
            const description = document.getElementById('referenceDescription').value;
            
            const formData = new FormData();
            formData.append('description', description);
            if (uploadedReferenceAttachmentIds.length > 0) {
                uploadedReferenceAttachmentIds.forEach(id => {
                    formData.append('attachments[]', id);
                });
            }
            
            try {
                const response = await axios.post(`/notices/${noticeId}/reference-materials`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        closeReferenceModal();
                        location.reload();
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.response?.data?.message || 'Failed to submit reference materials.',
                });
            }
        });

        // File upload handling for reference attachments
        const referenceAttachmentsInput = document.getElementById('referenceAttachments');
        const referenceDropZone = document.getElementById('referenceDropZone');
        const referenceAttachmentsPreview = document.getElementById('referenceAttachmentsPreview');

        referenceDropZone.addEventListener('click', () => {
            referenceAttachmentsInput.click();
        });

        referenceAttachmentsInput.addEventListener('change', async function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                await handleReferenceFilesUpload(files);
                referenceAttachmentsInput.value = '';
            }
        });

        async function handleReferenceFilesUpload(files) {
            for (const file of files) {
                if (file.size > 30 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: `File "${file.name}" exceeds 30MB limit.`,
                    });
                    return;
                }
            }

            const uploadFormData = new FormData();
            files.forEach(file => {
                uploadFormData.append('files[]', file);
            });

            try {
                const uploadResponse = await axios.post('{{ route("admin.media-library.store") }}', uploadFormData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (uploadResponse.data.success && uploadResponse.data.files) {
                    const newIds = uploadResponse.data.files.map(file => file.id);
                    uploadedReferenceAttachmentIds = [...uploadedReferenceAttachmentIds, ...newIds];
                    
                    uploadResponse.data.files.forEach(file => {
                        const isImage = file.type.startsWith('image/');
                        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                        const previewHtml = `
                            <div class="relative border rounded-lg p-2 attachment-item" data-file-id="${file.id}">
                                <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors delete-reference-attachment-btn" data-file-id="${file.id}">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                ${isImage ? 
                                    `<img src="${file.url}" alt="${file.name}" class="w-full h-24 object-cover rounded">` :
                                    isPdf ?
                                    `<div class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded">
                                        <i class="fas fa-file-pdf text-3xl text-red-500 mb-1"></i>
                                    </div>` :
                                    `<div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
                                        <i class="fas fa-file text-3xl text-gray-400"></i>
                                    </div>`
                                }
                                <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                            </div>
                        `;
                        referenceAttachmentsPreview.insertAdjacentHTML('beforeend', previewHtml);
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Error',
                    text: error.response?.data?.message || 'Failed to upload files.',
                });
            }
        }

        $(document).on('click', '.delete-reference-attachment-btn', function(e) {
            e.preventDefault();
            const fileId = $(this).data('file-id');
            uploadedReferenceAttachmentIds = uploadedReferenceAttachmentIds.filter(id => id !== fileId);
            $(this).closest('.attachment-item').remove();
        });
    </script>
</body>
</html>
