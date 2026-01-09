<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Referendums - Board Member Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="shortcut icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="apple-touch-icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize theme immediately before page renders to prevent flash
        (function() {
            const theme = localStorage.getItem('theme') || 
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @include('components.header-footer-styles')
    <style>
        /* Facebook Newsfeed Styles */
        .newsfeed-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 16px;
        }
        
        .referendums-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .referendums-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .fb-post-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 12px;
            overflow: hidden;
            transition: box-shadow 0.2s ease;
        }
        
        .dark .fb-post-card {
            background: #242526;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .fb-post-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .dark .fb-post-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }
        
        /* Post Header */
        .fb-post-header {
            padding: 12px 16px;
            display: flex;
            align-items: start;
            gap: 8px;
            border-bottom: 1px solid #e4e6eb;
            margin-bottom: 0;
            padding-bottom: 12px;
        }
        
        .dark .fb-post-header {
            border-bottom-color: #3a3b3c;
        }
        
        @media (min-width: 640px) {
            .fb-post-header {
                align-items: center;
                gap: 12px;
                padding-bottom: 16px;
            }
        }
        
        .profile-picture-container {
            position: relative;
            flex-shrink: 0;
            display: inline-block;
        }
        .profile-picture-container img {
            display: block;
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }
        
        .fb-post-avatar {
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid;
            border-color: #055498;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .fb-post-header-info {
            flex: 1;
            min-width: 0;
        }
        
        .fb-post-header-name {
            font-weight: 600;
            font-size: 15px;
            color: #050505;
            margin-bottom: 2px;
            line-height: 1.3333;
        }
        
        .dark .fb-post-header-name {
            color: #e4e6eb;
        }
        
        .fb-post-header-name a {
            color: inherit;
            text-decoration: none;
        }
        
        .fb-post-header-name a:hover {
            text-decoration: underline;
        }
        
        .fb-post-header-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #65676b;
        }
        
        @media (min-width: 640px) {
            .fb-post-header-meta {
                gap: 6px;
            }
        }
        
        .dark .fb-post-header-meta {
            color: #b0b3b8;
        }
        
        .online-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #3fbb46;
            border: 2px solid white;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        @media (min-width: 640px) {
            .online-indicator {
                width: 14px;
                height: 14px;
                border-width: 3px;
            }
        }
        .dark .online-indicator {
            border-color: #1e293b;
        }
        .offline-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #9ca3af;
            border: 2px solid white;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
        }
        @media (min-width: 640px) {
            .offline-indicator {
                width: 14px;
                height: 14px;
                border-width: 3px;
            }
        }
        .dark .offline-indicator {
            border-color: #1e293b;
        }
        
        @media (max-width: 475px) {
            .profile-picture-container img {
                width: 48px !important;
                height: 48px !important;
            }
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 4px;
        }
        
        .status-active {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10B981;
        }
        
        .status-expired {
            background-color: rgba(239, 68, 68, 0.15);
            color: #EF4444;
        }
        
        /* Post Content */
        .fb-post-content {
            padding: 12px 16px 16px;
        }
        
        .fb-post-title {
            font-size: 17px;
            font-weight: 600;
            color: #050505;
            line-height: 1.4118;
            margin-bottom: 12px;
            word-wrap: break-word;
        }
        
        .dark .fb-post-title {
            color: #e4e6eb;
        }
        
        .fb-post-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .fb-post-title a:hover {
            color: #1877f2;
            text-decoration: underline;
        }
        
        .dark .fb-post-title a:hover {
            color: #4599ff;
        }
        
        .fb-post-text {
            font-size: 15px;
            color: #050505;
            line-height: 1.6;
            word-wrap: break-word;
            margin-bottom: 0;
            text-indent: 0;
            padding: 0;
        }
        
        .dark .fb-post-text {
            color: #e4e6eb;
        }
        
        .fb-see-more-link {
            color: #1877f2;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            margin-left: 4px;
            transition: color 0.2s ease;
            display: inline;
            white-space: nowrap;
        }
        
        .dark .fb-see-more-link {
            color: #4599ff;
        }
        
        .fb-see-more-link:hover {
            text-decoration: underline;
            color: #166fe5;
        }
        
        .dark .fb-see-more-link:hover {
            color: #3578e5;
        }
        
        .fb-see-more-link i {
            font-size: 12px;
        }
        
        /* Post Attachments */
        .fb-post-attachments {
            margin: 0 -16px;
            max-height: 500px;
            overflow: hidden;
        }
        
        .fb-post-attachment-img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            display: block;
        }
        
        /* Post Stats */
        .fb-post-stats {
            padding: 10px 16px;
            border-top: 1px solid #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            color: #65676b;
        }
        
        .dark .fb-post-stats {
            border-top-color: #3a3b3c;
            color: #b0b3b8;
        }
        
        .fb-post-stats-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .fb-post-stats-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .fb-post-stats-link {
            color: inherit;
            text-decoration: none;
            cursor: pointer;
        }
        
        .fb-post-stats-link:hover {
            text-decoration: underline;
        }
        
        /* Vote Breakdown - Gen Z Design */
        .fb-vote-breakdown {
            padding: 16px;
            border-top: 1px solid #e4e6eb;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 0 0 8px 8px;
        }
        
        .dark .fb-vote-breakdown {
            border-top-color: #3a3b3c;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        
        .fb-vote-breakdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            gap: 12px;
        }
        
        .fb-vote-stat {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            justify-content: center;
        }
        
        .fb-vote-stat-accept {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%);
            color: #10B981;
            border: 2px solid rgba(16, 185, 129, 0.2);
        }
        
        .fb-vote-stat-decline {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%);
            color: #EF4444;
            border: 2px solid rgba(239, 68, 68, 0.2);
        }
        
        .dark .fb-vote-stat-accept {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.15) 100%);
            border-color: rgba(16, 185, 129, 0.3);
        }
        
        .dark .fb-vote-stat-decline {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.15) 100%);
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .fb-vote-stat-icon {
            font-size: 16px;
        }
        
        .fb-vote-bar {
            width: 100%;
            height: 10px;
            background: #e4e6eb;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .dark .fb-vote-bar {
            background: #3a3b3c;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .fb-vote-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #10B981 0%, #059669 50%, #047857 100%);
            border-radius: 20px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .fb-vote-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Post Actions */
        .fb-post-actions {
            padding: 4px 8px;
            border-top: 1px solid #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }
        
        .dark .fb-post-actions {
            border-top-color: #3a3b3c;
        }
        
        .fb-post-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 600;
            color: #65676b;
            text-decoration: none;
            transition: background-color 0.2s ease;
            border: none;
            background: none;
            cursor: pointer;
        }
        
        .dark .fb-post-action-btn {
            color: #b0b3b8;
        }
        
        .fb-post-action-btn:hover {
            background: #f0f2f5;
        }
        
        .dark .fb-post-action-btn:hover {
            background: #3a3b3c;
        }
        
        .fb-post-action-btn i {
            font-size: 20px;
        }
        
        .fb-post-action-btn.primary {
            color: #1877f2;
        }
        
        .dark .fb-post-action-btn.primary {
            color: #4599ff;
        }
        
        /* Empty State */
        .fb-empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .fb-empty-state i {
            font-size: 64px;
            color: #bcc0c4;
            margin-bottom: 16px;
        }
        
        .dark .fb-empty-state i {
            color: #3a3b3c;
        }
        
        .fb-empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #050505;
            margin-bottom: 8px;
        }
        
        .dark .fb-empty-state h3 {
            color: #e4e6eb;
        }
        
        .fb-empty-state p {
            font-size: 15px;
            color: #65676b;
        }
        
        .dark .fb-empty-state p {
            color: #b0b3b8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .newsfeed-container {
                padding: 12px 8px;
            }
            
            .fb-post-card {
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <!-- Referendums Newsfeed -->
    <div class="min-h-screen bg-[#f0f2f5] dark:bg-[#18191a]">
        <div class="newsfeed-container">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="text-2xl font-bold mb-1 text-[#050505] dark:text-[#e4e6eb]">Referendums</h1>
                <p class="text-sm text-[#65676b] dark:text-[#b0b3b8]">View and participate in board referendums</p>
            </div>

                <!-- Referendums Feed -->
                @if($referendums->count() > 0)
                    <div class="referendums-grid">
                    @foreach($referendums as $referendum)
                        @php
                            $isExpired = $referendum->isExpired();
                            $acceptCount = $referendum->acceptVotes()->count();
                            $declineCount = $referendum->declineVotes()->count();
                            $totalVotes = $referendum->votes()->count();
                            $totalComments = $referendum->allComments()->count();
                            
                            // Get creator profile picture
                            $creatorProfilePic = 'https://ui-avatars.com/api/?name=' . urlencode($referendum->creator->first_name . ' ' . $referendum->creator->last_name) . '&size=80&background=1877f2&color=fff';
                            if ($referendum->creator->profile_picture) {
                                $media = \App\Models\MediaLibrary::find($referendum->creator->profile_picture);
                                if ($media) {
                                    $creatorProfilePic = asset('storage/' . $media->file_path);
                                }
                            }
                            
                            // Get first image attachment for preview
                            $firstImage = null;
                            if ($referendum->attachments && count($referendum->attachments) > 0) {
                                foreach ($referendum->attachments as $attachmentId) {
                                    $attachmentMedia = \App\Models\MediaLibrary::find($attachmentId);
                                    if ($attachmentMedia && str_starts_with($attachmentMedia->file_type, 'image/')) {
                                        $firstImage = asset('storage/' . $attachmentMedia->file_path);
                                        break;
                                    }
                                }
                            }
                        @endphp
                        
                        <div class="fb-post-card">
                            <!-- Post Header -->
                            <div class="fb-post-header">
                                @php
                                    $isCreatorOnline = $referendum->creator->is_online ?? false;
                                    $totalUsers = \App\Models\User::where('privilege', '!=', 'admin')->count();
                                    $allowedUsersCount = $referendum->allowedUsers()->where('privilege', '!=', 'admin')->count();
                                    $isPublic = $totalUsers === $allowedUsersCount;
                                @endphp
                                <div class="profile-picture-container flex-shrink-0 relative">
                                    <img src="{{ $creatorProfilePic }}" alt="{{ $referendum->creator->first_name }} {{ $referendum->creator->last_name }}" class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover border-2 shadow-sm block fb-post-avatar" style="border-color: #055498; aspect-ratio: 1/1;">
                                    <div class="{{ $isCreatorOnline ? 'online-indicator' : 'offline-indicator' }}"></div>
                                </div>
                                <div class="fb-post-header-info">
                                    <div class="flex items-center flex-wrap gap-1.5 sm:gap-2 mb-1">
                                        <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white truncate">
                                            <a href="{{ route('referendums.show', $referendum->id) }}" class="text-gray-800 dark:text-white hover:underline">
                                                {{ $referendum->creator->first_name }} {{ $referendum->creator->last_name }}
                                            </a>
                                        </h3>
                                        <span class="status-badge {{ $isExpired ? 'status-expired' : 'status-active' }}">
                                            {{ $isExpired ? 'Expired' : 'Active' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1 sm:gap-1.5 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-0.5 sm:gap-1 whitespace-nowrap">
                                            <i class="fas fa-clock" style="font-size: 9px;"></i>
                                            <span>{{ $referendum->created_at->diffForHumans() }}</span>
                                        </span>
                                        @if($referendum->expires_at)
                                            <span class="text-gray-300 dark:text-gray-600 hidden sm:inline">·</span>
                                            <span class="flex items-center gap-0.5 sm:gap-1 {{ $isExpired ? 'text-red-500 dark:text-red-400' : 'text-orange-500 dark:text-orange-400' }} whitespace-nowrap">
                                                <i class="fas fa-calendar-times" style="font-size: 9px;"></i>
                                                <span class="hidden xs:inline">Expires: </span>
                                                <span>{{ $referendum->expires_at->format('M d, Y') }}</span>
                                            </span>
                                        @endif
                                        <span class="text-gray-300 dark:text-gray-600 hidden sm:inline">·</span>
                                        <span class="flex items-center gap-0.5 sm:gap-1 whitespace-nowrap">
                                            @if($isPublic)
                                                <i class="fas fa-globe-americas" style="font-size: 9px;"></i>
                                                <span>Public</span>
                                            @else
                                                <i class="fas fa-users" style="font-size: 9px;"></i>
                                                <span>Custom</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Post Content -->
                            <div class="fb-post-content">
                                <h2 class="fb-post-title">
                                    <a href="{{ route('referendums.show', $referendum->id) }}">
                                        {{ $referendum->title }}
                                    </a>
                                </h2>
                                <div class="fb-post-text">
                                    {{ Str::limit(strip_tags($referendum->content), 300) }}@if(strlen(strip_tags($referendum->content)) > 300) <a href="{{ route('referendums.show', $referendum->id) }}" class="fb-see-more-link">See more...</a>@endif
                                </div>
                            </div>
                            
                            <!-- Post Attachment (Image) -->
                            @if($firstImage)
                                <div class="fb-post-attachments">
                                    <a href="{{ route('referendums.show', $referendum->id) }}">
                                        <img src="{{ $firstImage }}" alt="Referendum attachment" class="fb-post-attachment-img">
                                    </a>
                                </div>
                            @endif
                            
                            <!-- Vote Breakdown -->
                            @if($totalVotes > 0)
                                <div class="fb-vote-breakdown">
                                    <div class="fb-vote-breakdown-header">
                                        <div class="fb-vote-stat fb-vote-stat-accept">
                                            <i class="fas fa-check-circle fb-vote-stat-icon"></i>
                                            <span>Accept: {{ $acceptCount }}</span>
                                        </div>
                                        <div class="fb-vote-stat fb-vote-stat-decline">
                                            <i class="fas fa-times-circle fb-vote-stat-icon"></i>
                                            <span>Decline: {{ $declineCount }}</span>
                                        </div>
                                    </div>
                                    <div class="fb-vote-bar">
                                        @php
                                            $acceptPercent = $totalVotes > 0 ? ($acceptCount / $totalVotes) * 100 : 0;
                                        @endphp
                                        <div class="fb-vote-bar-fill" style="width: {{ $acceptPercent }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Post Stats -->
                            <div class="fb-post-stats">
                                <div class="fb-post-stats-left">
                                    @if($totalVotes > 0)
                                        <span class="text-[#1877f2] dark:text-[#4599ff]">
                                            <i class="fas fa-thumbs-up"></i>
                                            {{ $totalVotes }}
                                        </span>
                                    @endif
                                </div>
                                <div class="fb-post-stats-right">
                                    @if($totalComments > 0)
                                        <a href="{{ route('referendums.show', $referendum->id) }}#comments" class="fb-post-stats-link">
                                            {{ $totalComments }} {{ Str::plural('comment', $totalComments) }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Post Actions -->
                            <div class="fb-post-actions">
                                <a href="{{ route('referendums.show', $referendum->id) }}" class="fb-post-action-btn primary">
                                    <i class="fas fa-vote-yea"></i>
                                    <span>Vote</span>
                                </a>
                                <a href="{{ route('referendums.show', $referendum->id) }}#comments" class="fb-post-action-btn">
                                    <i class="fas fa-comment"></i>
                                    <span>Comment</span>
                                </a>
                                <a href="{{ route('referendums.show', $referendum->id) }}" class="fb-post-action-btn">
                                    <i class="fas fa-share"></i>
                                    <span>View</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 mb-4">
                        {{ $referendums->links() }}
                    </div>
                @else
                    <div class="fb-empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Referendums Available</h3>
                        <p>You don't have access to any referendums at this time.</p>
                    </div>
                @endif
        </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    </script>
    
    @include('components.footer')
    
    <!-- Global PDF Modal - Available on all pages -->
    @include('components.pdf-modal')
</body>
</html>

