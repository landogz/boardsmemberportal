<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $notice->title }} - Communication</title>
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
        .badge-postponed {
            background: rgba(107, 114, 128, 0.2);
            color: #4b5563;
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
            grid-auto-flow: column;
            grid-auto-columns: minmax(0, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            overflow-x: auto;
        }
        
        .dark .notice-meta-grid {
            border-top-color: #334155;
        }
        
        .notice-meta-grid .meta-item {
            min-width: 0;
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
            min-width: 0;
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
            /* Allow long values (like venue) to wrap nicely */
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        
        .dark .meta-value {
            color: #f1f5f9;
        }
        
        .notice-byline {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.8125rem;
            color: #64748b;
            margin-top: 0.625rem;
            margin-bottom: 0.25rem;
            letter-spacing: 0.01em;
        }
        .dark .notice-byline {
            color: #94a3b8;
        }
        .notice-byline .byline-item {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }
        .notice-byline .byline-avatar {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
        }
        .dark .notice-byline .byline-avatar {
            border-color: #475569;
        }
        .notice-byline .byline-item i {
            font-size: 0.75rem;
            color: #055498;
            opacity: 0.9;
        }
        .dark .notice-byline .byline-item i {
            color: #38bdf8;
        }
        .notice-byline .byline-label {
            font-weight: 500;
            color: #475569;
        }
        .dark .notice-byline .byline-label {
            color: #94a3b8;
        }
        .notice-byline .byline-value {
            font-weight: 600;
            color: #334155;
        }
        .dark .notice-byline .byline-value {
            color: #e2e8f0;
        }
        .notice-byline .byline-sep {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #cbd5e1;
            flex-shrink: 0;
        }
        .dark .notice-byline .byline-sep {
            background: #475569;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0 1rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
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
        
        .response-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .response-divider {
            width: 1px;
            height: 2.5rem;
            background: #e2e8f0;
            flex-shrink: 0;
        }
        .dark .response-divider {
            background: #475569;
        }
        .btn-change-response {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            font-size: 0.8125rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s, background 0.2s;
            background: transparent;
        }
        .btn-change-response i {
            font-size: 0.75rem;
            opacity: 0.85;
        }
        .btn-change-accept {
            color: #059669;
        }
        .btn-change-accept:hover {
            background: rgba(5, 150, 105, 0.08);
        }
        .btn-change-decline {
            color: #b91c1c;
        }
        .btn-change-decline:hover {
            background: rgba(185, 28, 28, 0.08);
        }
        .btn-change-agenda {
            color: #055498;
        }
        .btn-change-agenda:hover {
            background: rgba(5, 84, 152, 0.08);
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
            word-wrap: break-word;
        }
        .notice-description p {
            margin: 0 0 0.25em 0 !important;
        }
        .notice-description p:last-child {
            margin-bottom: 0 !important;
        }
        .notice-description p:empty,
        .notice-description p:has(br:only-child) {
            display: none;
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
        /* Restore proper styling for lists inside the description */
        .notice-description ul,
        .notice-description ol {
            margin: 0 0 0.75em 1.5rem !important;
            padding-left: 1.25rem !important;
        }
        .notice-description ul {
            list-style-type: disc;
            list-style-position: outside;
        }
        .notice-description ol {
            list-style-type: decimal;
            list-style-position: outside;
        }
        .notice-description li {
            margin: 0.125em 0 0.125em 0 !important;
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

        /* Top invite actions: professional, clean */
        .invite-actions-wrap {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        .dark .invite-actions-wrap {
            border-top-color: #334155;
        }
        .invite-actions-wrap .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .invite-actions-wrap .btn-action {
            flex: none;
            min-width: 140px;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s, box-shadow 0.2s;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .invite-actions-wrap .btn-action i {
            font-size: 0.875rem;
        }
        .invite-actions-wrap .btn-accept {
            background: #059669;
            color: #fff;
            border-color: #047857;
        }
        .invite-actions-wrap .btn-accept:hover {
            background: #047857;
            border-color: #065f46;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
        }
        .invite-actions-wrap .btn-decline {
            background: #fff;
            color: #b91c1c;
            border-color: #e5e7eb;
        }
        .dark .invite-actions-wrap .btn-decline {
            background: #334155;
            color: #fca5a5;
            border-color: #475569;
        }
        .invite-actions-wrap .btn-decline:hover {
            background: #b91c1c;
            border-color: #991b1b;
            color: #fff;
        }
        .dark .invite-actions-wrap .btn-decline:hover {
            background: #991b1b;
            border-color: #b91c1c;
            color: #fff;
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
                grid-auto-columns: minmax(120px, 1fr);
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
            <span>Back to Communication</span>
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
                @if(($notice->status ?? null) === 'postponed')
                    <span class="notice-badge badge-postponed ml-1.5">
                        <i class="fas fa-pause-circle"></i>
                        Postponed
                    </span>
                @endif
                
                <h1 class="notice-title">{{ $notice->title }}</h1>
                
                <p class="notice-byline">
                    <span class="byline-item">
                        @php
                            $creatorProfilePic = 'https://ui-avatars.com/api/?name=' . urlencode($notice->creator->first_name . ' ' . $notice->creator->last_name) . '&size=64&background=055498&color=fff&bold=true';
                            if ($notice->creator->profile_picture) {
                                $creatorMedia = \App\Models\MediaLibrary::find($notice->creator->profile_picture);
                                if ($creatorMedia) {
                                    $creatorProfilePic = asset('storage/' . $creatorMedia->file_path);
                                }
                            }
                        @endphp
                        <img src="{{ $creatorProfilePic }}" alt="{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}" class="byline-avatar">
                        <span class="byline-label">Created by</span>
                        <span class="byline-value">{{ $notice->creator->first_name }} {{ $notice->creator->last_name }}</span>
                    </span>
                    <span class="byline-sep" aria-hidden="true"></span>
                    <span class="byline-item">
                        <i class="fas fa-calendar"></i>
                        <span class="byline-value">{{ $notice->created_at->format('M d, Y') }}</span>
                    </span>
                </p>
                
                @if($attendanceConfirmation && ($notice->status ?? null) !== 'postponed')
                    <div class="response-row">
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
                        @if(in_array($attendanceConfirmation->status, ['accepted', 'declined']))
                            @if(!$isMeetingDone)
                                <span class="response-divider" aria-hidden="true"></span>
                                <button type="button" class="btn-change-response {{ $attendanceConfirmation->status === 'accepted' ? 'btn-change-decline' : 'btn-change-accept' }}" onclick="{{ $attendanceConfirmation->status === 'accepted' ? 'declineNotice(' . $notice->id . ')' : 'acceptNotice(' . $notice->id . ', this, \'' . addslashes($notice->meeting_type ?? '') . '\')' }}">
                                    @if($attendanceConfirmation->status === 'accepted')
                                        <i class="fas fa-edit"></i>
                                        <span>Change response to Decline</span>
                                    @else
                                        <i class="fas fa-edit"></i>
                                        <span>Change response to ATTEND</span>
                                    @endif
                                </button>
                            @endif
                            @if($attendanceConfirmation->status === 'accepted' && !$isMeetingDone && !$agendaRequest && ($notice->status ?? null) !== 'postponed')
                                <button type="button" class="btn-change-response btn-change-agenda" onclick="requestAgendaInclusion({{ $notice->id }})">
                                    <i class="fas fa-plus"></i>
                                    <span>Request Agenda Inclusion</span>
                                </button>
                            @endif
                            @if($attendanceConfirmation->status === 'accepted' && !$isMeetingDone && $agendaRequest)
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
                            @endif
                        @endif
                    </div>
                @endif

                @if(($notice->status ?? null) !== 'postponed' && $notice->notice_type !== 'Notice of Postponement' && $notice->notice_type !== 'Agenda' && !$isMeetingDone && (!$attendanceConfirmation || $attendanceConfirmation->status === 'pending'))
                    <div class="invite-actions-wrap">
                        <div class="action-buttons">
                            <button type="button" class="btn-action btn-accept" onclick="acceptNotice({{ $notice->id }}, this, '{{ addslashes($notice->meeting_type ?? '') }}')">
                                <i class="fas fa-check"></i>
                                <span>Approve</span>
                            </button>
                            <button type="button" class="btn-action btn-decline" onclick="declineNotice({{ $notice->id }})">
                                <i class="fas fa-times"></i>
                                <span>Decline</span>
                            </button>
                        </div>
                    </div>
                @endif
                
                <div class="notice-meta-grid">
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
                    
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Meeting Type</div>
                            <div class="meta-value">{{ ucfirst($notice->meeting_type) }}</div>
                        </div>
                    </div>
                    
                    @if(in_array($notice->meeting_type, ['onsite', 'hybrid']) && $notice->venue)
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Venue</div>
                                <div class="meta-value">{{ $notice->venue }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($notice->meeting_type, ['online', 'hybrid']) && $notice->meeting_link && (!isset($hasDeclined) || !$hasDeclined))
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Meeting Link</div>
                                <div class="meta-value">
                                    <a href="{{ $notice->meeting_link }}" target="_blank" rel="noopener" class="text-[#055498] hover:underline font-medium break-all">{{ $notice->meeting_link }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notice Content Card -->
        <div class="notice-content-card">
            @if(isset($hasDeclined) && $hasDeclined)
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Invitation declined</p>
                            <p class="mt-1">You have declined this invitation. Meeting materials and links are no longer accessible.</p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="content-section">
                @if($notice->description)
                    <h2 class="section-title">Description</h2>
                    <div class="notice-description">
                        {!! $notice->description !!}
                    </div>
                @endif

                @if((!isset($hasDeclined) || !$hasDeclined) && !empty($referenceFiles))
                    <div class="attachments-section">
                        <h2 class="section-title">Reference Materials</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">All meeting materials for this notice (attachments, reference materials, agenda items, board regulations and resolutions).</p>
                        <div class="attachments-grid">
                            @foreach($referenceFiles as $file)
                                @php
                                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $isPdf = $ext === 'pdf';
                                    $fileUrl = asset('storage/' . $file->file_path);
                                @endphp
                                <div class="attachment-card">
                                    @if($isImage)
                                        <div class="attachment-preview">
                                            <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}">
                                        </div>
                                    @elseif($isPdf)
                                        <div class="attachment-preview pdf" onclick="openGlobalPdfModal('{{ $fileUrl }}', '{{ addslashes($file->file_name) }}')">
                                            <i class="fas fa-file-pdf text-5xl text-red-500"></i>
                                        </div>
                                    @else
                                        <div class="attachment-preview file">
                                            <i class="fas fa-file text-5xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="attachment-name" title="{{ $file->file_name }}">
                                        {{ $file->file_name }}
                                    </div>
                                    @if($isPdf)
                                        <a href="javascript:void(0)" onclick="openGlobalPdfModal('{{ $fileUrl }}', '{{ addslashes($file->file_name) }}')" class="attachment-action">
                                            <i class="fas fa-eye"></i>
                                            <span>View PDF</span>
                                        </a>
                                    @else
                                        <a href="{{ route('notices.attachment.download', ['id' => $notice->id, 'mediaId' => $file->media_id]) }}" target="_blank" class="attachment-action">
                                            <i class="fas fa-download"></i>
                                            <span>Open</span>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions Card hidden for now. Remove the @if(false) wrapper to restore it. --}}
        @if(false)
            <!-- Actions Card -->
            <div class="actions-card">
                <div class="actions-content">
                    @if(!$attendanceConfirmation || $attendanceConfirmation->status === 'pending')
                        <div class="action-buttons">
                            <button class="btn-action btn-accept" onclick="acceptNotice({{ $notice->id }}, this, '{{ addslashes($notice->meeting_type ?? '') }}')">
                                <i class="fas fa-check"></i>
                                <span>Accept Invitation</span>
                            </button>
                            <button class="btn-action btn-decline" onclick="declineNotice({{ $notice->id }})">
                                <i class="fas fa-times"></i>
                                <span>Decline Invitation</span>
                            </button>
                        </div>
                    @elseif(false && $attendanceConfirmation->status === 'accepted' && !$referenceMaterial)
                        {{-- Submit Reference Materials button hidden for now - remove "false &&" to re-enable --}}
                        <div class="action-buttons">
                            <button class="btn-action btn-agenda" onclick="submitReferenceMaterial({{ $notice->id }})">
                                <i class="fas fa-file-upload"></i>
                                <span>Submit Reference Materials</span>
                            </button>
                        </div>
                    @elseif($attendanceConfirmation->status === 'accepted' && $referenceMaterial)
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
        @endif
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
                        id="declineSubmitBtn"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
                    >
                        <span id="declineSubmitBtnText">Submit</span>
                        <span id="declineSubmitBtnSpinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
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
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF (Max: 100MB per file)</p>
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
                        id="agendaSubmitBtn"
                        class="flex-1 px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
                    >
                        <span id="agendaSubmitBtnText">Submit Request</span>
                        <span id="agendaSubmitBtnSpinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
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
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF (Max: 100MB per file)</p>
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
                        id="referenceSubmitBtn"
                        class="flex-1 px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
                    >
                        <span id="referenceSubmitBtnText">Submit Materials</span>
                        <span id="referenceSubmitBtnSpinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
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
                    acceptNotice({{ $notice->id }}, null, '{{ addslashes($notice->meeting_type ?? '') }}');
                }, 500);
            });
        @elseif(isset($autoAction) && $autoAction === 'decline')
            $(document).ready(function() {
                setTimeout(function() {
                    declineNotice({{ $notice->id }});
                }, 500);
            });
        @endif

        // Open reference materials modal when arriving with ?open=reference (e.g. from /notices)
        $(document).ready(function() {
            if (window.location.search.indexOf('open=reference') !== -1) {
                setTimeout(function() { submitReferenceMaterial({{ $notice->id }}); }, 300);
            }
        });

        const acceptSpinnerHtml = '<span class="inline-flex items-center justify-center gap-2"><svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Accepting...</span>';
        function acceptNotice(noticeId, btn, meetingTypeParam) {
            const meetingType = (meetingTypeParam != null && meetingTypeParam !== '') ? meetingTypeParam : '{{ $notice->meeting_type }}';
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
                if (!result.isConfirmed) return;

                const proceedWithAccept = (attendanceMode) => {
                    const originalHtml = btn ? btn.innerHTML : null;
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = acceptSpinnerHtml;
                    }
                    axios.post(`/notices/${noticeId}/accept`, attendanceMode ? { attendance_mode: attendanceMode } : {})
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
                            } else if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = originalHtml;
                            }
                        })
                        .catch(error => {
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = originalHtml;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.response?.data?.message || 'Failed to accept invitation.',
                            });
                        });
                };

                // For hybrid meetings, ask user to choose how they'll attend (loading on Continue button)
                if (meetingType === 'hybrid') {
                    Swal.fire({
                        title: 'How will you attend?',
                        input: 'radio',
                        inputOptions: {
                            onsite: 'Onsite (in person)',
                            online: 'Online (virtual)'
                        },
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Please select how you will attend.';
                            }
                        },
                        confirmButtonText: 'Continue',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        showLoaderOnConfirm: true,
                        preConfirm: (value) => {
                            return axios.post(`/notices/${noticeId}/accept`, { attendance_mode: value })
                                .then(response => {
                                    if (response.data.success) return response.data;
                                    throw new Error(response.data.message || 'Failed to accept.');
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: error.response?.data?.message || 'Failed to accept invitation.'
                                    });
                                    throw error;
                                });
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.value.message || 'Invitation accepted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => { location.reload(); });
                        }
                    });
                } else {
                    proceedWithAccept(null);
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

        function submitReferenceMaterial(noticeId, e) {
            if (e) e.preventDefault();
            currentNoticeId = noticeId;
            document.getElementById('referenceNoticeId').value = noticeId;
            document.getElementById('referenceDescription').value = '';
            uploadedReferenceAttachmentIds = [];
            document.getElementById('referenceAttachmentsPreview').innerHTML = '';
            document.getElementById('referenceModal').classList.remove('hidden');
        }

        // Decline form submission
        document.getElementById('declineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const noticeId = document.getElementById('declineNoticeId').value;
            const reason = document.getElementById('declineReason').value;
            const submitBtn = document.getElementById('declineSubmitBtn');
            const submitBtnText = document.getElementById('declineSubmitBtnText');
            const submitBtnSpinner = document.getElementById('declineSubmitBtnSpinner');

            submitBtn.disabled = true;
            submitBtnText.textContent = 'Declining...';
            submitBtnSpinner.classList.remove('hidden');

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
                    } else {
                        submitBtn.disabled = false;
                        submitBtnText.textContent = 'Submit';
                        submitBtnSpinner.classList.add('hidden');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Submit';
                    submitBtnSpinner.classList.add('hidden');
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
            const description = document.getElementById('agendaDescription').value.trim();
            const submitBtn = document.getElementById('agendaSubmitBtn');
            const submitBtnText = document.getElementById('agendaSubmitBtnText');
            const submitBtnSpinner = document.getElementById('agendaSubmitBtnSpinner');

            if (!description) {
                Swal.fire({ icon: 'error', title: 'Required', text: 'Please enter a description.' });
                return;
            }

            submitBtn.disabled = true;
            submitBtnText.textContent = 'Submitting...';
            submitBtnSpinner.classList.remove('hidden');

            try {
                const response = await axios.post(`/notices/${noticeId}/agenda-inclusion`, {
                    description: description,
                    attachments: uploadedAttachmentIds
                });
                if (response.data.success) {
                    closeAgendaModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Submitted',
                        text: response.data.message || 'Your agenda inclusion request has been submitted.'
                    }).then(() => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Submit Request';
                    submitBtnSpinner.classList.add('hidden');
                    Swal.fire({ icon: 'error', title: 'Error', text: response.data.message || 'Failed to submit.' });
                }
            } catch (error) {
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Submit Request';
                submitBtnSpinner.classList.add('hidden');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to submit agenda inclusion request.'
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
                if (file.size > 100 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: `File "${file.name}" exceeds 100MB limit.`,
                    });
                    return;
                }
            }

            const uploadFormData = new FormData();
            files.forEach(file => {
                uploadFormData.append('files[]', file);
            });

            try {
                const uploadResponse = await axios.post('{{ route("notices.upload-attachment") }}', uploadFormData, {
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
            const description = document.getElementById('referenceDescription').value.trim();
            const submitBtn = document.getElementById('referenceSubmitBtn');
            const submitBtnText = document.getElementById('referenceSubmitBtnText');
            const submitBtnSpinner = document.getElementById('referenceSubmitBtnSpinner');

            if (!description) {
                Swal.fire({
                    icon: 'error',
                    title: 'Required',
                    text: 'Please enter a description for your reference materials.',
                });
                return;
            }

            submitBtn.disabled = true;
            submitBtnText.textContent = 'Submitting...';
            submitBtnSpinner.classList.remove('hidden');

            try {
                const response = await axios.post(`/notices/${noticeId}/reference-materials`, {
                    description: description,
                    attachments: uploadedReferenceAttachmentIds
                });

                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Submitted!',
                        text: response.data.message || 'Your reference materials have been submitted for review.',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        closeReferenceModal();
                        // Reload without query parameters (e.g., remove ?open=reference)
                        window.location.href = window.location.pathname;
                    });
                } else {
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Submit Materials';
                    submitBtnSpinner.classList.add('hidden');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to submit reference materials.',
                    });
                }
            } catch (error) {
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Submit Materials';
                submitBtnSpinner.classList.add('hidden');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
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
                if (file.size > 100 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: `File "${file.name}" exceeds 100MB limit.`,
                    });
                    return;
                }
            }

            const uploadFormData = new FormData();
            files.forEach(file => {
                uploadFormData.append('files[]', file);
            });

            try {
                const uploadResponse = await axios.post('{{ route("notices.upload-attachment") }}', uploadFormData, {
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
