<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notices - Board Member Portal</title>
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
        .notices-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .notices-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .notices-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #055498 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .dark .page-title {
            background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .page-subtitle {
            font-size: 1.125rem;
            color: #64748b;
            font-weight: 500;
        }
        
        .dark .page-subtitle {
            color: #94a3b8;
        }
        
        .notice-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }
        
        .dark .notice-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .notice-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .notice-card-link {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        
        .notice-actions {
            position: relative;
            z-index: 10;
        }
        
        .notice-actions button,
        .notice-actions a {
            position: relative;
            z-index: 11;
        }
        
        .notice-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }
        
        .dark .notice-header {
            border-bottom-color: #334155;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
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
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .dark .notice-title {
            color: #f1f5f9;
        }
        
        .notice-title-link {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .notice-title-link:hover {
            color: #055498;
        }
        
        .dark .notice-title-link:hover {
            color: #0ea5e9;
        }
        
        .notice-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        
        .dark .meta-item {
            color: #94a3b8;
        }
        
        .meta-item i {
            width: 1rem;
            text-align: center;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
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
        
        .notice-content {
            padding: 1.5rem;
        }
        
        .notice-description {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: #475569;
            margin-bottom: 1rem;
        }
        
        .dark .notice-description {
            color: #cbd5e1;
        }
        
        .notice-details {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }
        
        .dark .notice-details {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }
        
        .detail-label {
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .dark .detail-label {
            color: #94a3b8;
        }
        
        .detail-value {
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
        
        .notice-actions {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }
        
        .dark .notice-actions {
            border-top-color: #334155;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn-action {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9375rem;
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
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            min-height: 400px;
        }
        
        .dark .empty-state {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
        
        .dark .empty-icon {
            color: #475569;
        }
        
        .empty-text {
            font-size: 1.125rem;
            color: #64748b;
            font-weight: 500;
            text-align: center;
        }
        
        .dark .empty-text {
            color: #94a3b8;
        }
        
        .pagination-wrapper {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .notices-container {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .notice-header,
            .notice-content,
            .notice-actions {
                padding: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .notice-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    @include('components.header')
    
    <div class="notices-container">
        <div class="page-header">
            <h1 class="page-title">Notices</h1>
            <p class="page-subtitle">View and respond to meeting notices</p>
        </div>

        @if($notices->count() > 0)
            <div class="notices-grid">
            @foreach($notices as $notice)
                <div class="notice-card" onclick="window.location.href='{{ route('notices.show', $notice->id) }}'">
                    <a href="{{ route('notices.show', $notice->id) }}" class="notice-card-link"></a>
                    <div class="notice-header">
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
                        
                        <h2 class="notice-title">
                            {{ $notice->title }}
                        </h2>
                        
                        <div class="notice-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $notice->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($notice->meeting_date)
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Meeting: {{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($notice->meeting_time)
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ \Carbon\Carbon::parse($notice->meeting_time)->format('g:i A') }}</span>
                                </div>
                            @endif
                            @if($notice->creator)
                                <div class="meta-item">
                                    @php
                                        $creatorProfilePic = 'https://ui-avatars.com/api/?name=' . urlencode($notice->creator->first_name . ' ' . $notice->creator->last_name) . '&size=32&background=055498&color=fff&bold=true';
                                        if ($notice->creator->profile_picture) {
                                            $media = \App\Models\MediaLibrary::find($notice->creator->profile_picture);
                                            if ($media) {
                                                $creatorProfilePic = asset('storage/' . $media->file_path);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $creatorProfilePic }}" alt="{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}" class="w-4 h-4 rounded-full object-cover border border-gray-300 dark:border-gray-600" style="margin-right: 0.25rem;">
                                    <span>{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}</span>
                                </div>
                            @endif
                        </div>
                        
                        @if(isset($attendanceConfirmations[$notice->id]))
                            @php
                                $status = $attendanceConfirmations[$notice->id];
                            @endphp
                            <div class="status-badge status-{{ $status }}">
                                @if($status === 'accepted')
                                    <i class="fas fa-check-circle"></i>
                                    <span>You have accepted this invitation</span>
                                @elseif($status === 'declined')
                                    <i class="fas fa-times-circle"></i>
                                    <span>You have declined this invitation</span>
                                @else
                                    <i class="fas fa-clock"></i>
                                    <span>Response pending</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="notice-content">
                        @if($notice->description)
                            <div class="notice-description">
                                {{ Str::limit(strip_tags($notice->description), 200) }}
                            </div>
                        @endif
                        
                        <div class="notice-details">
                            <div class="detail-row">
                                <span class="detail-label">Meeting Type</span>
                                <span class="detail-value">{{ ucfirst($notice->meeting_type) }}</span>
                            </div>
                            @if(in_array($notice->meeting_type, ['online', 'hybrid']) && $notice->meeting_link)
                                <div class="detail-row">
                                    <span class="detail-label">Meeting Link</span>
                                    <span class="detail-value">
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
                                        <a href="{{ $notice->meeting_link }}" target="_blank" class="detail-link inline-flex items-center gap-1.5 px-2 py-1 rounded text-xs font-medium transition-colors hover:opacity-90" onclick="event.stopPropagation();" style="color: {{ $platformColor }}; background: {{ $platformColor }}15; border: 1px solid {{ $platformColor }}40;">
                                            @if($platform === 'zoom')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 5.605L12 11.979 8.075 13.826l-1.97-5.605L12 6.275l5.894 1.946z"/>
                                                </svg>
                                            @elseif($platform === 'google-meet')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M19.53 9.75L15.75 6H8.25L4.47 9.75a.75.75 0 0 0-.22.53v3.44c0 .2.08.39.22.53L8.25 18h7.5l3.78-3.75a.75.75 0 0 0 .22-.53v-3.44a.75.75 0 0 0-.22-.53zM12 13.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                                </svg>
                                            @elseif($platform === 'teams')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M19.5 4.5c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2s2-.897 2-2v-11c0-1.103-.897-2-2-2zm-15 0c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2s2-.897 2-2v-11c0-1.103-.897-2-2-2zm7.5 0c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2s2-.897 2-2v-11c0-1.103-.897-2-2-2z"/>
                                                </svg>
                                            @else
                                                <i class="fas {{ $platformIcon }}" style="font-size: 12px;"></i>
                                            @endif
                                            <span>Join {{ $platformName }}</span>
                                        </a>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="notice-actions">
                        <div class="action-buttons">
                            @if(!isset($attendanceConfirmations[$notice->id]) || $attendanceConfirmations[$notice->id] === 'pending')
                                <button class="btn-action btn-accept" onclick="event.stopPropagation(); acceptNotice({{ $notice->id }});">
                                    <i class="fas fa-check"></i>
                                    <span>Accept</span>
                                </button>
                                <button class="btn-action btn-decline" onclick="event.stopPropagation(); declineNotice({{ $notice->id }});">
                                    <i class="fas fa-times"></i>
                                    <span>Decline</span>
                                </button>
                            @elseif(isset($attendanceConfirmations[$notice->id]) && $attendanceConfirmations[$notice->id] === 'accepted')
                                @php
                                    $isMeetingDone = $notice->meeting_date && \Carbon\Carbon::parse($notice->meeting_date)->isPast();
                                @endphp
                                @if(!$isMeetingDone && !isset($agendaRequests[$notice->id]))
                                    <button class="btn-action btn-agenda" onclick="event.stopPropagation(); requestAgendaInclusion({{ $notice->id }});">
                                        <i class="fas fa-plus"></i>
                                        <span>Request Agenda Inclusion</span>
                                    </button>
                                @elseif($isMeetingDone && !isset($referenceMaterials[$notice->id]))
                                    <button class="btn-action btn-agenda" onclick="event.stopPropagation(); submitReferenceMaterial({{ $notice->id }});">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Submit Reference Materials</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            
            <div class="pagination-wrapper">
                {{ $notices->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="empty-text">No notices available</p>
            </div>
        @endif
    </div>

    @include('components.footer')

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

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        let uploadedAttachmentIds = [];
        let currentNoticeId = null;

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
            Swal.fire({
                icon: 'info',
                title: 'Function Not Yet Approved',
                text: 'This function is currently under development and has not been approved yet.',
                confirmButtonColor: '#055498',
                confirmButtonText: 'OK'
            });
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

        function submitReferenceMaterial(noticeId, e) {
            if (e) e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Function Not Yet Approved',
                text: 'This function is currently under development and has not been approved yet.',
                confirmButtonColor: '#055498',
                confirmButtonText: 'OK'
            });
        }
    </script>
</body>
</html>
