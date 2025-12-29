<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $referendum->title }} - Referendums</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="shortcut icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="apple-touch-icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        /* Facebook-like Post Styles */
        .fb-post {
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            margin-bottom: 0;
        }
        .dark .fb-post {
            background: transparent;
            box-shadow: none;
        }
        .fb-post-header {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .fb-post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .fb-post-header-info {
            flex: 1;
        }
        .fb-post-header-name {
            font-weight: 600;
            font-size: 15px;
            color: #050505;
            margin-bottom: 2px;
        }
        .dark .fb-post-header-name {
            color: #e4e6eb;
        }
        .fb-post-header-time {
            font-size: 13px;
            color: #65676b;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .dark .fb-post-header-time {
            color: #b0b3b8;
        }
        .fb-post-content {
            padding: 0 16px 12px;
        }
        .fb-post-text {
            font-size: 15px;
            color: #050505;
            line-height: 1.3333;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .dark .fb-post-text {
            color: #e4e6eb;
        }
        .fb-post-attachments {
            margin: 0 -16px;
        }
        .fb-post-attachment {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
            background: #f0f2f5;
        }
        .dark .fb-post-attachment {
            background: #242526;
        }
        .fb-post-attachments .grid {
            display: grid;
        }
        .fb-post-attachments .grid img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .fb-post-attachments .grid > div {
            background: #f0f2f5;
        }
        .dark .fb-post-attachments .grid > div {
            background: #242526;
        }
        .aspect-square {
            aspect-ratio: 1 / 1;
        }
        /* Facebook-style grid for multiple images */
        .fb-post-attachments .grid.grid-cols-2 {
            grid-template-columns: 1fr 1fr;
        }
        .fb-post-attachments .grid.grid-cols-2 > div:first-child:nth-last-child(3),
        .fb-post-attachments .grid.grid-cols-2 > div:first-child:nth-last-child(2) {
            grid-column: 1 / -1;
        }
        .fb-post-engagement {
            padding: 8px 16px;
            border-top: 1px solid #dadde1;
            border-bottom: 1px solid #dadde1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dark .fb-post-engagement {
            border-color: #3e4042;
        }
        .fb-post-reactions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .fb-reaction-count {
            font-size: 15px;
            color: #65676b;
            cursor: pointer;
        }
        .dark .fb-reaction-count {
            color: #b0b3b8;
        }
        .fb-reaction-count:hover {
            text-decoration: underline;
        }
        .fb-post-actions {
            display: flex;
            gap: 0;
        }
        .fb-action-button {
            flex: 1;
            padding: 8px;
            border: none;
            background: transparent;
            color: #65676b;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .dark .fb-action-button {
            color: #b0b3b8;
        }
        .fb-action-button:hover {
            background: #f2f2f2;
        }
        .dark .fb-action-button:hover {
            background: #2d3748;
        }
        .fb-action-button.active {
            color: #1877f2;
        }
        .dark .fb-action-button.active {
            color: #8ab4f8;
        }
        .fb-action-button.accept.active {
            color: #10b981;
        }
        .fb-action-button.decline.active {
            color: #ef4444;
        }
        .fb-comments-section {
            padding: 8px 16px 16px;
        }
        .fb-comment {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .fb-comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
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
        .online-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #10b981;
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
        .dark .offline-indicator {
            border-color: #1e293b;
        }
        @media (min-width: 640px) {
            .offline-indicator {
                width: 14px;
                height: 14px;
                border-width: 3px;
            }
        }
        
        /* Extra small screen optimizations */
        @media (max-width: 475px) {
            .profile-picture-container img {
                width: 48px !important;
                height: 48px !important;
            }
        }
        .fb-comment-content {
            flex: 1;
            background: #f0f2f5;
            border-radius: 18px;
            padding: 8px 12px;
        }
        .dark .fb-comment-content {
            background: #2d3748;
        }
        .fb-comment-author {
            font-weight: 600;
            font-size: 13px;
            color: #050505;
            margin-bottom: 2px;
        }
        .dark .fb-comment-author {
            color: #e4e6eb;
        }
        .fb-comment-text-wrapper {
            position: relative;
        }
        .fb-comment-text {
            font-size: 15px;
            color: #050505;
            line-height: 1.3333;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .fb-comment-text.truncated {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: pre-wrap;
        }
        .dark .fb-comment-text {
            color: #e4e6eb;
        }
        .fb-comment-actions {
            display: flex;
            gap: 16px;
            margin-top: 4px;
            padding-left: 12px;
        }
        .fb-comment-action {
            font-size: 12px;
            color: #65676b;
            cursor: pointer;
            font-weight: 600;
        }
        .dark .fb-comment-action {
            color: #b0b3b8;
        }
        .fb-comment-action:hover {
            text-decoration: underline;
        }
        .fb-comment-reply {
            margin-left: 0;
            margin-top: 8px;
        }
        .replies-container {
            margin-top: 8px;
        }
        .view-replies-btn, .hide-replies-btn {
            display: inline-flex;
            align-items: center;
            padding: 4px 0;
            transition: all 0.2s;
            cursor: pointer;
        }
        .view-replies-btn.hidden, .hide-replies-btn.hidden {
            display: none !important;
        }
        .view-replies-btn:hover, .hide-replies-btn:hover {
            text-decoration: underline;
        }
        .view-replies-btn i, .hide-replies-btn i {
            font-size: 10px;
            transition: transform 0.2s;
        }
        .view-replies-btn.active i {
            transform: rotate(180deg);
        }
        .fb-comment-input {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .fb-comment-input-box {
            flex: 1;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 80px 8px 12px;
            border: none;
            font-size: 15px;
            color: #050505;
            resize: none;
            min-height: 39px;
            max-height: 200px;
            width: 100%;
        }
        .dark .fb-comment-input-box {
            background: #2d3748;
            color: #e4e6eb;
        }
        .fb-comment-input-box:focus {
            outline: none;
        }
        .fb-comment-input-box::placeholder {
            color: #65676b;
        }
        .dark .fb-comment-input-box::placeholder {
            color: #b0b3b8;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        .status-active {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }
        .status-expired {
            background-color: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }
        
        /* Shimmer animation for progress bar */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
        
        /* Dark mode vote stat boxes */
        .dark .vote-stat-accept {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.15) 100%) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }
        
        .dark .vote-stat-decline {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.15) 100%) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #2d3748;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4a5568;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-[#18191a] text-[#050505] dark:text-[#e4e6eb] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <!-- Facebook-like Post Container -->
    <div class="min-h-screen py-8 bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('referendums.index') }}" class="inline-flex items-center text-sm font-medium text-[#1877f2] hover:text-[#166fe5] transition-colors duration-200 group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i>
                    Back to Referendums
                </a>
            </div>

            <!-- Facebook Post - Two Row Layout -->
            <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- First Row: Header, Title, Description, and Attachments Preview (Full Width, Centered) -->
                <div class="w-full">
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 border-b border-gray-200 dark:border-gray-700">
                        <!-- Post Header, Title & Description (Single Card) -->
                        <div class="mb-6">
                            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-[#1e293b] dark:to-[#0f172a] rounded-xl p-4 sm:p-6 transition-all duration-300">
                                <!-- Post Header -->
                                <div class="flex items-start sm:items-center space-x-2 sm:space-x-3 mb-3 sm:mb-4 pb-3 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                                    @php
                                        $creatorProfileMedia = $referendum->creator->profile_picture ? \App\Models\MediaLibrary::find($referendum->creator->profile_picture) : null;
                                        $creatorProfileUrl = $creatorProfileMedia ? asset('storage/' . $creatorProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($referendum->creator->first_name . ' ' . $referendum->creator->last_name) . '&size=150&background=1877f2&color=fff';
                                        $isCreatorOnline = $referendum->creator->is_online ?? false;
                                    @endphp
                                    <div class="profile-picture-container flex-shrink-0 relative">
                                        <img src="{{ $creatorProfileUrl }}" alt="{{ $referendum->creator->first_name }} {{ $referendum->creator->last_name }}" class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover border-2 shadow-sm block" style="border-color: #055498; aspect-ratio: 1/1;">
                                        <div class="{{ $isCreatorOnline ? 'online-indicator' : 'offline-indicator' }}"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center flex-wrap gap-1.5 sm:gap-2 mb-1">
                                            <h3 class="text-sm sm:text-base font-bold text-gray-800 dark:text-white truncate">
                                                {{ $referendum->creator->first_name }} {{ $referendum->creator->last_name }}
                                            </h3>
                                            <span class="status-badge {{ $referendum->isExpired() ? 'status-expired' : 'status-active' }}">
                                                {{ $referendum->isExpired() ? 'Expired' : 'Active' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-1 sm:gap-1.5 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-0.5 sm:gap-1 whitespace-nowrap">
                                                <i class="fas fa-clock" style="font-size: 9px;"></i>
                                                <span>{{ $referendum->created_at->diffForHumans() }}</span>
                                            </span>
                                            @if($referendum->expires_at)
                                                <span class="text-gray-300 dark:text-gray-600 hidden sm:inline">·</span>
                                                <span class="flex items-center gap-0.5 sm:gap-1 {{ $referendum->isExpired() ? 'text-red-500 dark:text-red-400' : 'text-orange-500 dark:text-orange-400' }} whitespace-nowrap">
                                                    <i class="fas fa-calendar-times" style="font-size: 9px;"></i>
                                                    <span class="hidden xs:inline">Expires: </span>
                                                    <span>{{ $referendum->expires_at->format('M d, Y') }}</span>
                                                </span>
                                            @endif
                                            <span class="text-gray-300 dark:text-gray-600 hidden sm:inline">·</span>
                                            @php
                                                $totalUsers = \App\Models\User::where('privilege', '!=', 'admin')->count();
                                                $allowedUsersCount = $referendum->allowedUsers()->where('privilege', '!=', 'admin')->count();
                                                $isPublic = $totalUsers === $allowedUsersCount;
                                            @endphp
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
                                
                                <!-- Title & Description -->
                                <div>
                                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2 sm:mb-3 leading-tight tracking-tight break-words">{{ $referendum->title }}</h1>
                                    <div class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap break-words">{{ $referendum->content }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        @if($referendum->attachments && count($referendum->attachments) > 0)
                            @php
                                $imageAttachments = [];
                                $pdfAttachments = [];
                                $otherAttachments = [];
                                foreach($referendum->attachments as $mediaId) {
                                    $media = \App\Models\MediaLibrary::find($mediaId);
                                    if($media) {
                                        if(Str::startsWith($media->file_type, 'image/')) {
                                            $imageAttachments[] = $media;
                                        } elseif(Str::endsWith(strtolower($media->file_name), '.pdf') || $media->file_type === 'application/pdf') {
                                            $pdfAttachments[] = $media;
                                        } else {
                                            $otherAttachments[] = $media;
                                        }
                                    }
                                }
                                $previewAttachments = array_merge($imageAttachments, $pdfAttachments);
                            @endphp
                            @if(count($previewAttachments) > 0)
                                <div class="fb-post-attachments flex justify-center items-center">
                                    <div class="w-full overflow-x-auto">
                                        <div class="flex gap-3 justify-center items-center min-h-[300px] px-4">
                                            @foreach($previewAttachments as $index => $media)
                                        @php
                                            $isImage = Str::startsWith($media->file_type, 'image/');
                                        @endphp
                                                <div class="flex-shrink-0 w-64 h-64 rounded-lg overflow-hidden cursor-pointer hover:opacity-90 transition-all duration-200 hover:scale-105 shadow-md">
                                        @if($isImage)
                                            <img 
                                                src="{{ asset('storage/' . $media->file_path) }}" 
                                                alt="{{ $media->file_name }}" 
                                                            class="w-full h-full object-cover"
                                                                onclick="openAttachmentGallery({{ $index }})"
                                                            >
                                                        @else
                                                            <div 
                                                            class="w-full h-full bg-gray-100 dark:bg-gray-800 flex flex-col items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                                                onclick="openAttachmentGallery({{ $index }})"
                                                            >
                                                            <i class="fas fa-file-pdf text-5xl text-red-500 mb-3"></i>
                                                            <p class="text-xs text-gray-600 dark:text-gray-300 px-3 text-center truncate w-full font-medium">{{ $media->file_name }}</p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Click to view</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                                        </div>
                                </div>
                            @endif
                            @if(count($otherAttachments) > 0)
                                <div class="px-4 py-2">
                                    <div class="space-y-2">
                                        @foreach($otherAttachments as $media)
                                            <div 
                                                class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer"
                                                onclick="window.open('{{ asset('storage/' . $media->file_path) }}', '_blank')"
                                            >
                                                <i class="fas fa-file text-2xl text-gray-400"></i>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $media->file_name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Click to download</p>
                                                </div>
                                                <i class="fas fa-download text-gray-400"></i>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="fb-post-attachments bg-gray-100 dark:bg-gray-800 flex items-center justify-center rounded-lg" style="min-height: 400px;">
                                <div class="text-center text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-image text-6xl mb-4"></i>
                                    <p>No attachments</p>
                                </div>
                            </div>
                        @endif
                                </div>
                            </div>

                <!-- Second Row: Details (Full Width) -->
                <div class="w-full p-6">
                    <div class="mx-auto space-y-5">
                            <!-- Voting Section Card -->
                            @if(!$referendum->isExpired())
                                <div class="bg-gradient-to-br from-white to-gray-50 dark:from-[#1e293b] dark:to-[#0f172a] rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:shadow-lg">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <i class="fas fa-vote-yea mr-2 text-[#055498]"></i>
                                        Cast Your Vote
                                    </h3>
                                    @if($userVote)
                                        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <p class="text-sm text-blue-800 dark:text-blue-300 flex items-center">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                You voted: <strong class="ml-1">{{ ucfirst($userVote->vote) }}</strong>
                                            </p>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-3 mb-4">
                                            <button 
                                                type="button" 
                                                id="voteAcceptBtn"
                                                class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center transform hover:scale-[1.02] active:scale-[0.98]"
                                            >
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Accept
                                            </button>
                                            <button 
                                                type="button" 
                                                id="voteDeclineBtn"
                                                class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-semibold text-sm transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center transform hover:scale-[1.02] active:scale-[0.98]"
                                            >
                                                <i class="fas fa-times-circle mr-2"></i>
                                                Decline
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Vote Statistics - Gen Z Design -->
                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Total Votes</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $totalVotes }}</span>
                                        </div>
                                        <div class="flex gap-3 mb-4">
                                            <div class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all vote-stat-accept" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%); color: #10B981; border: 2px solid rgba(16, 185, 129, 0.2);">
                                                <i class="fas fa-check-circle text-base"></i>
                                                <span>Accept: {{ $acceptCount }}</span>
                                                </div>
                                            <div class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all vote-stat-decline" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%); color: #EF4444; border: 2px solid rgba(239, 68, 68, 0.2);">
                                                <i class="fas fa-times-circle text-base"></i>
                                                <span>Decline: {{ $declineCount }}</span>
                                            </div>
                                        </div>
                                        @if($totalVotes > 0)
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner" style="box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                @php
                                                    $acceptPercent = ($acceptCount / $totalVotes) * 100;
                                                @endphp
                                                <div class="bg-gradient-to-r from-green-500 via-green-600 to-green-700 h-full rounded-full transition-all duration-500 relative overflow-hidden" style="width: {{ $acceptPercent }}%; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="bg-gradient-to-br from-white to-gray-50 dark:from-[#1e293b] dark:to-[#0f172a] rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:shadow-lg">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <i class="fas fa-info-circle text-orange-500"></i>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Referendum Expired</h3>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">
                                        This referendum has expired. Voting is no longer available.
                                    </p>
                                    <!-- Final Vote Statistics - Gen Z Design -->
                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Final Results</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $totalVotes }}</span>
                                        </div>
                                        <div class="flex gap-3 mb-4">
                                            <div class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all vote-stat-accept" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%); color: #10B981; border: 2px solid rgba(16, 185, 129, 0.2);">
                                                <i class="fas fa-check-circle text-base"></i>
                                                <span>Accept: {{ $acceptCount }}</span>
                                                </div>
                                            <div class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-sm transition-all vote-stat-decline" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%); color: #EF4444; border: 2px solid rgba(239, 68, 68, 0.2);">
                                                <i class="fas fa-times-circle text-base"></i>
                                                <span>Decline: {{ $declineCount }}</span>
                                            </div>
                                        </div>
                                        @if($totalVotes > 0)
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner" style="box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                @php
                                                    $acceptPercent = ($acceptCount / $totalVotes) * 100;
                                                @endphp
                                                <div class="bg-gradient-to-r from-green-500 via-green-600 to-green-700 h-full rounded-full transition-all duration-500 relative overflow-hidden" style="width: {{ $acceptPercent }}%; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Comments Section Card -->
                            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-[#1e293b] dark:to-[#0f172a] rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:shadow-lg">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                                        <i class="fas fa-comments mr-2 text-[#055498]"></i>
                                        Comments
                                    </h3>
                                    @php
                                        $totalAllComments = $referendum->allComments()->count();
                                    @endphp
                                    <span id="commentsCount" class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-full">{{ $totalAllComments }}</span>
                                </div>

                                @if($totalMainComments > 0)
                                    <div id="commentsList" class="space-y-4">
                                        @foreach($mainComments as $comment)
                                            @include('referendums.partials.comment', ['comment' => $comment, 'referendum' => $referendum])
                                        @endforeach
                                    </div>
                                    
                                    @if($totalMainComments > 5)
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <button 
                                                type="button" 
                                                id="viewMoreCommentsBtn"
                                                class="w-full text-center text-sm font-medium text-[#1877f2] hover:text-[#166fe5] transition-colors py-2"
                                                data-loaded="{{ $mainComments->count() }}"
                                                data-total="{{ $totalMainComments }}"
                                            >
                                                View More Comments
                                                <span class="ml-2 text-gray-500 dark:text-gray-400">{{ $mainComments->count() }} of {{ $totalMainComments }}</span>
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-2">
                                        <i class="fas fa-comment-slash text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No comments yet.</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Be the first to comment!</p>
                                    </div>
                                @endif
                            </div>
                            </div>

                        <!-- Comment Input -->
                        <div class="bg-white dark:bg-[#1e293b] border-t-2 border-gray-200 dark:border-gray-700 pt-4 pb-4 mt-5 shadow-lg">
                                @if(!$referendum->isExpired())
                                    <div class="flex items-start space-x-2">
                                        @php
                                            $userProfileMedia = Auth::user()->profile_picture ? \App\Models\MediaLibrary::find(Auth::user()->profile_picture) : null;
                                            $userProfileUrl = $userProfileMedia ? asset('storage/' . $userProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) . '&size=150&background=1877f2&color=fff';
                                            $userFullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                                        @endphp
                                        <img src="{{ $userProfileUrl }}" alt="{{ $userFullName }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1">
                                        <div class="flex-1 min-w-0">
                                            <div class="relative">
                                        <textarea 
                                            id="commentInput" 
                                            rows="1" 
                                                    placeholder="Comment as {{ $userFullName }}..."
                                                    class="w-full px-4 py-2.5 pr-20 bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none focus:outline-none focus:ring-2 focus:ring-[#1877f2] focus:border-[#1877f2] transition-all duration-200 overflow-hidden"
                                                    style="min-height: 40px; max-height: 120px; line-height: 1.5;"
                                        ></textarea>
                                                <div class="absolute right-2 bottom-2 flex items-center gap-1">
                                                    <button 
                                                        type="button" 
                                                        id="commentEmojiBtn"
                                                        class="chat-emoji-btn p-2 text-yellow-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        id="sendCommentBtn"
                                                        class="p-2 text-[#1877f2] hover:bg-[#1877f2] hover:text-white rounded-full transition-all duration-200 opacity-100 pointer-events-auto transform hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        style="top: auto; transform: none;"
                                                    >
                                                        <i class="fas fa-paper-plane text-sm"></i>
                                                    </button>
                                                </div>
                                                <!-- Emoji Picker -->
                                                <div id="commentEmojiPicker" class="absolute bottom-12 right-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 hidden flex flex-col" style="max-height: 300px;">
                                                    @include('components.emoji-picker')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center py-3">
                                        <i class="fas fa-lock mr-2"></i>
                                        Commenting is disabled for expired referendums
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        const referendumId = {{ $referendum->id }};
        const isExpired = {{ $referendum->isExpired() ? 'true' : 'false' }};
        const hasVoted = {{ $userVote ? 'true' : 'false' }};

        // Configure SweetAlert Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        // Auto-resize comment textarea (like Facebook)
        function autoResizeTextarea(textarea) {
            if (!textarea) return;
            textarea.style.height = 'auto';
            const newHeight = Math.min(textarea.scrollHeight, 120); // Max 120px
            textarea.style.height = newHeight + 'px';
        }
        
        // Emoji picker functionality for comment input
        const commentEmojiBtn = document.getElementById('commentEmojiBtn');
        const commentEmojiPicker = document.getElementById('commentEmojiPicker');
        const commentInput = document.getElementById('commentInput');
        
        if (commentEmojiBtn && commentEmojiPicker) {
            // Toggle emoji picker
            commentEmojiBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                commentEmojiPicker.classList.toggle('hidden');
            });
            
            // Hide emoji picker when clicking outside
            document.addEventListener('click', function(e) {
                if (commentEmojiPicker && !commentEmojiPicker.contains(e.target) && e.target !== commentEmojiBtn) {
                    commentEmojiPicker.classList.add('hidden');
                }
            });
            
            // Emoji selection
            const emojiItems = commentEmojiPicker.querySelectorAll('.emoji-item');
            emojiItems.forEach(item => {
                item.addEventListener('click', function() {
                    const emoji = this.getAttribute('data-emoji');
                    if (commentInput) {
                        const cursorPos = commentInput.selectionStart;
                        const textBefore = commentInput.value.substring(0, cursorPos);
                        const textAfter = commentInput.value.substring(cursorPos);
                        commentInput.value = textBefore + emoji + textAfter;
                        commentInput.selectionStart = commentInput.selectionEnd = cursorPos + emoji.length;
                        commentInput.focus();
                        autoResizeTextarea(commentInput);
                        
                        // Enable send button if content exists
                        const content = commentInput.value.trim();
                        const sendBtn = $('#sendCommentBtn');
                        if (content.length > 0) {
                            sendBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                        }
                    }
                });
            });
            
            // Emoji category switching
            const categoryBtns = commentEmojiPicker.querySelectorAll('.emoji-category-btn');
            const categoryDivs = commentEmojiPicker.querySelectorAll('.emoji-category');
            
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active button
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show/hide category divs
                    categoryDivs.forEach(div => {
                        if (div.getAttribute('data-category') === category) {
                            div.classList.remove('hidden');
                            div.classList.add('active');
                        } else {
                            div.classList.add('hidden');
                            div.classList.remove('active');
                        }
                    });
                });
            });
            
            // Emoji search functionality
            const emojiSearchInput = commentEmojiPicker.querySelector('.emoji-search-input');
            if (emojiSearchInput) {
                emojiSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    if (searchTerm === '') {
                        // Show all emojis
                        emojiItems.forEach(item => {
                            item.style.display = '';
                        });
                    } else {
                        // Simple emoji search (you can enhance this with a proper emoji library)
                        emojiItems.forEach(item => {
                            const emoji = item.getAttribute('data-emoji');
                            // For now, just show all if search is active (can be enhanced)
                            item.style.display = '';
                        });
                    }
                });
            }
        }
        
        // Auto-resize on input and keyup (to catch Shift+Enter)
        $('#commentInput').on('input keyup', function() {
            autoResizeTextarea(this);
            
            // Enable/disable send button based on content
            const content = $(this).val().trim();
            const sendBtn = $('#sendCommentBtn');
            if (content.length > 0) {
                sendBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            } else {
                sendBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            }
        });
        
        // Also resize on keydown for immediate feedback
        $('#commentInput').on('keydown', function(e) {
            // If Shift+Enter is pressed, allow new line and resize
            if (e.key === 'Enter' && e.shiftKey) {
                setTimeout(() => {
                    autoResizeTextarea(this);
                }, 10);
            }
            // If Enter (without Shift), it will be handled by the submit handler below
        });
        
        // Initial resize
        const initialCommentInput = document.getElementById('commentInput');
        if (initialCommentInput) {
            autoResizeTextarea(initialCommentInput);
        }

        // Scroll to comment if URL has hash (e.g., from notification link)
        function scrollToComment(commentId) {
            if (!commentId) return;
            
            // Try to find the comment element by ID first, then by data attribute
            let commentElement = null;
            let attempts = 0;
            const maxAttempts = 30; // Try for 3 seconds (30 * 100ms)
            
            const tryScroll = function() {
                attempts++;
                
                // Try to find by ID
                commentElement = document.getElementById('comment-' + commentId);
                
                // If not found by ID, try to find by data attribute
                if (!commentElement) {
                    const $commentByData = $('[data-comment-id="' + commentId + '"]');
                    if ($commentByData.length > 0) {
                        commentElement = $commentByData[0];
                        // Add ID if it doesn't have one
                        if (!commentElement.id) {
                            commentElement.id = 'comment-' + commentId;
                        }
                    }
                }
                
                if (commentElement) {
                    // If comment is in a hidden replies container, expand all parent containers
                    const $comment = $(commentElement);
                    let needsExpansion = false;
                    
                    // Find all parent reply containers that are hidden
                    const expandContainers = function() {
                        const $repliesContainer = $comment.closest('.replies-container.hidden');
                        if ($repliesContainer.length > 0) {
                            needsExpansion = true;
                            const $parentComment = $repliesContainer.closest('.fb-comment');
                            const parentCommentId = $parentComment.data('comment-id');
                            const $viewRepliesBtn = $('.view-replies-btn[data-comment-id="' + parentCommentId + '"]');
                            if ($viewRepliesBtn.length > 0) {
                                $viewRepliesBtn.click();
                                // Recursively check for more hidden containers
                                setTimeout(() => {
                                    expandContainers();
                                }, 300);
                                return;
                            }
                        }
                        
                        // All containers expanded, now scroll
                        if (needsExpansion) {
                            setTimeout(() => {
                                // Re-find element after expansion
                                commentElement = document.getElementById('comment-' + commentId) || $('[data-comment-id="' + commentId + '"]')[0];
                                if (commentElement) {
                                    scrollToElement(commentElement);
                                }
                            }, 400);
                        } else {
                            // No expansion needed, scroll immediately
                            scrollToElement(commentElement);
                        }
                    };
                    
                    expandContainers();
                } else if (attempts < maxAttempts) {
                    // Comment not found yet, try again
                    setTimeout(tryScroll, 100);
                } else {
                    console.log('Comment not found after multiple attempts:', commentId);
                    // Try one more time after a longer delay (in case comments are loaded via AJAX)
                    setTimeout(() => {
                        commentElement = document.getElementById('comment-' + commentId) || $('[data-comment-id="' + commentId + '"]')[0];
                        if (commentElement) {
                            scrollToElement(commentElement);
                        }
                    }, 1000);
                }
            };
            
            // Start trying after a short delay to ensure DOM is ready
            setTimeout(tryScroll, 200);
        }
        
        function scrollToElement(element) {
            if (!element) return;
            
            // Scroll to comment with smooth behavior
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight the comment briefly
            element.style.transition = 'background-color 0.3s ease';
            element.style.backgroundColor = 'rgba(24, 119, 242, 0.15)';
            element.style.borderRadius = '8px';
            element.style.padding = '8px';
            element.style.margin = '-8px';
            
            setTimeout(() => {
                element.style.backgroundColor = '';
                element.style.borderRadius = '';
                element.style.padding = '';
                element.style.margin = '';
            }, 2000);
        }
        
        // Check for hash on page load
        $(document).ready(function() {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#comment-')) {
                const commentId = hash.replace('#comment-', '');
                scrollToComment(commentId);
            }
        });
        
        // Also check when window hash changes (for dynamic navigation)
        $(window).on('hashchange', function() {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#comment-')) {
                const commentId = hash.replace('#comment-', '');
                scrollToComment(commentId);
            }
        });
        
        // Listen for dynamically added comments (MutationObserver)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                const hash = window.location.hash;
                if (hash && hash.startsWith('#comment-')) {
                    const commentId = hash.replace('#comment-', '');
                    const commentElement = document.getElementById('comment-' + commentId) || $('[data-comment-id="' + commentId + '"]')[0];
                    if (commentElement && !commentElement.classList.contains('scrolled-to')) {
                        commentElement.classList.add('scrolled-to');
                        scrollToComment(commentId);
                    }
                }
            });
            
            // Observe the comments list container
            const commentsList = document.getElementById('commentsList');
            if (commentsList) {
                observer.observe(commentsList, {
                    childList: true,
                    subtree: true
                });
            }
        }

        // Check and truncate long comments (more than 4 lines) - including replies
        function checkAndTruncateComments() {
            // Check all comment texts (main comments and replies) - only check visible ones
            $('.fb-comment-text:visible').each(function() {
                const $text = $(this);
                const $wrapper = $text.closest('.fb-comment-text-wrapper');
                
                // Skip if wrapper doesn't exist (shouldn't happen, but safety check)
                if ($wrapper.length === 0) return;
                
                // Skip if the parent comment/reply is hidden
                if ($text.closest('.hidden').length > 0 && !$text.closest('.replies-container').hasClass('hidden')) {
                    // If it's in a replies container that's not hidden, check it
                } else if ($text.closest('.hidden').length > 0) {
                    return; // Skip hidden elements
                }
                
                const $seeMoreBtn = $wrapper.find('.see-more-btn');
                const $seeLessBtn = $wrapper.find('.see-less-btn');
                
                // Reset to check actual height - temporarily remove truncated class
                $text.removeClass('truncated');
                
                // Force a reflow to get accurate scrollHeight
                void $text[0].offsetHeight;
                
                // Get computed styles for accurate calculation
                const computedStyle = window.getComputedStyle($text[0]);
                const lineHeight = parseFloat(computedStyle.lineHeight) || parseFloat(computedStyle.fontSize) * 1.5;
                const maxHeight = lineHeight * 4; // 4 lines
                const currentHeight = $text[0].scrollHeight;
                
                // Check if content exceeds 4 lines (with small buffer for rounding)
                if (currentHeight > maxHeight + 2) {
                    // Truncate - content exceeds 4 lines
                    $text.addClass('truncated');
                    $seeMoreBtn.removeClass('hidden');
                    $seeLessBtn.addClass('hidden');
                } else {
                    // Content fits within 4 lines
                    $seeMoreBtn.addClass('hidden');
                    $seeLessBtn.addClass('hidden');
                }
            });
        }
        
        // See More / See Less functionality
        $(document).on('click', '.see-more-btn', function() {
            const $wrapper = $(this).closest('.fb-comment-text-wrapper');
            const $text = $wrapper.find('.fb-comment-text');
            const $seeMoreBtn = $(this);
            const $seeLessBtn = $wrapper.find('.see-less-btn');
            
            $text.removeClass('truncated');
            $seeMoreBtn.addClass('hidden');
            $seeLessBtn.removeClass('hidden');
        });
        
        $(document).on('click', '.see-less-btn', function() {
            const $wrapper = $(this).closest('.fb-comment-text-wrapper');
            const $text = $wrapper.find('.fb-comment-text');
            const $seeMoreBtn = $wrapper.find('.see-more-btn');
            const $seeLessBtn = $(this);
            
            $text.addClass('truncated');
            $seeMoreBtn.removeClass('hidden');
            $seeLessBtn.addClass('hidden');
        });
        
        // Check comments on page load
        $(document).ready(function() {
            setTimeout(function() {
                checkAndTruncateComments();
            }, 100);
        });

        // Voting functionality
        $('#voteAcceptBtn, #voteDeclineBtn').on('click', async function() {
            if (isExpired || hasVoted) {
                return;
            }

            const vote = $(this).attr('id') === 'voteAcceptBtn' ? 'accept' : 'decline';
            const voteText = vote === 'accept' ? 'Accept' : 'Decline';

            const result = await Swal.fire({
                title: 'Confirm Your Vote',
                text: `Are you sure you want to vote "${voteText}"? This action cannot be changed.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: vote === 'accept' ? '#10B981' : '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: `Yes, vote ${voteText}`,
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    const response = await axios.post(`/referendums/${referendumId}/vote`, {
                        vote: vote
                    });

                    if (response.data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Vote recorded successfully!'
                        });
                        
                        // Reload page to show updated vote status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to record vote. Please try again.'
                    });
                }
            }
        });

        // Function to render a single reply (recursively handles nested replies)
        function renderReply(reply) {
            if (!reply || !reply.user) {
                console.error('Invalid reply object:', reply);
                return '';
            }
            
            const userId = '{{ Auth::id() }}';
            const canDeleteReply = reply.user && reply.user.id === userId;
            const canEditReply = reply.user && reply.user.id === userId && !isExpired;
            const replyContent = escapeHtml(reply.content || '').replace(/\n/g, '<br>');
            const replyName = escapeHtml(reply.user.name || '');
            const replyProfilePic = escapeHtml((reply.user.profile_picture || ''));
            const isReplyerOnline = (reply.user.is_online || false);
            const replyOnlineClass = isReplyerOnline ? 'online-indicator' : 'offline-indicator';
            // Format date: if less than 20 seconds old, show "just now"
            let replyCreatedAt = '';
            if (reply.created_at_human) {
                replyCreatedAt = reply.created_at_human;
            } else if (reply.created_at) {
                // Parse the date and check if it's less than 20 seconds old
                const replyDate = new Date(reply.created_at);
                const now = new Date();
                const secondsDiff = Math.floor((now - replyDate) / 1000);
                replyCreatedAt = secondsDiff < 20 ? 'Just Now' : reply.created_at;
            } else {
                replyCreatedAt = 'Just Now';
            }
            replyCreatedAt = escapeHtml(replyCreatedAt);
            @php
                $currentUserProfileMedia = Auth::user()->profile_picture ? \App\Models\MediaLibrary::find(Auth::user()->profile_picture) : null;
                $currentUserProfileUrl = $currentUserProfileMedia ? asset('storage/' . $currentUserProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) . '&size=150&background=1877f2&color=fff';
            @endphp
            const currentUserProfileUrl = '{{ $currentUserProfileUrl }}';
            
            // Build nested replies HTML if they exist
            let nestedRepliesHtml = '';
            if (reply.replies && reply.replies.length > 0) {
                const nestedRepliesCount = reply.replies.length;
                const nestedRepliesText = nestedRepliesCount === 1 ? 'reply' : 'replies';
                nestedRepliesHtml = '<div class="mt-2">' +
                    '<div class="mb-2">' +
                    '<button type="button" class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer" data-comment-id="' + reply.id + '">' +
                    '<i class="fas fa-chevron-down mr-1"></i>' +
                    'View all ' + nestedRepliesCount + ' ' + nestedRepliesText +
                    '</button>' +
                    '<button type="button" class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer hidden" data-comment-id="' + reply.id + '">' +
                    '<i class="fas fa-chevron-up mr-1"></i>' +
                    'Hide replies' +
                    '</button>' +
                    '</div>' +
                    '<div class="replies-container hidden mt-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700">';
                reply.replies.forEach(function(nestedReply) {
                    nestedRepliesHtml += renderReply(nestedReply);
                });
                nestedRepliesHtml += '</div>' +
                    '</div>';
            }
            
            return '<div class="fb-comment-reply">' +
                '<div class="fb-comment" id="comment-' + reply.id + '" data-comment-id="' + reply.id + '">' +
                '<div class="profile-picture-container">' +
                '<img src="' + replyProfilePic + '" alt="' + replyName + '" class="fb-comment-avatar">' +
                '<div class="' + replyOnlineClass + '"></div>' +
                '</div>' +
                '<div class="flex-1">' +
                '<div class="fb-comment-content">' +
                '<div class="fb-comment-author">' + replyName + '</div>' +
                '<div class="fb-comment-text-wrapper">' +
                '<div class="fb-comment-text" data-full-text="' + escapeHtml(reply.content || '') + '">' + replyContent + '</div>' +
                '<button type="button" class="see-more-btn hidden text-xs font-semibold text-[#1877f2] hover:underline mt-1 cursor-pointer">See more</button>' +
                '<button type="button" class="see-less-btn hidden text-xs font-semibold text-[#1877f2] hover:underline mt-1 cursor-pointer">See less</button>' +
                '</div>' +
                '</div>' +
                '<div class="fb-comment-actions">' +
                (!isExpired ? '<span class="fb-comment-action reply-btn" data-comment-id="' + reply.id + '">Reply</span>' : '') +
                (canEditReply ? '<span class="fb-comment-action edit-comment-btn text-[#1877f2]" data-comment-id="' + reply.id + '">Edit</span>' : '') +
                (canDeleteReply ? '<span class="fb-comment-action delete-comment-btn text-red-500" data-comment-id="' + reply.id + '">Delete</span>' : '') +
                '<span class="text-xs text-gray-500 dark:text-gray-400">' + replyCreatedAt + '</span>' +
                '</div>' +
                (canEditReply ? '<div class="edit-comment-container hidden mt-2">' +
                    '<div class="flex items-start space-x-2">' +
                    '<img src="' + currentUserProfileUrl + '" alt="Your profile" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1">' +
                    '<div class="flex-1">' +
                    '<textarea class="edit-comment-textarea w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white resize-none focus:outline-none focus:ring-2 focus:ring-[#1877f2] focus:border-[#1877f2] transition-all" rows="2" data-comment-id="' + reply.id + '">' + (reply.content || '').replace(/<br>/g, '\n') + '</textarea>' +
                    '<div class="flex items-center justify-end gap-2 mt-2">' +
                    '<button type="button" class="cancel-edit-btn text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-semibold">Cancel</button>' +
                    '<button type="button" class="save-edit-btn text-xs bg-[#1877f2] text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-[#166fe5] transition-colors" data-comment-id="' + reply.id + '">Save</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' : '') +
                nestedRepliesHtml +
                '</div>' +
                '</div>' +
                '</div>';
        }

        // Function to submit comment
        async function submitComment() {
                if (isExpired) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Expired',
                        text: 'This referendum has expired. Commenting is no longer available.'
                    });
                    return;
                }

            const content = $('#commentInput').val().trim();
                if (!content) {
                    return;
                }

                try {
                    const response = await axios.post(`/referendums/${referendumId}/comments`, {
                        content: content
                    });

                    if (response.data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Comment posted successfully!'
                    });
                    
                    // Clear input and reset height
                    const textarea = document.getElementById('commentInput');
                    textarea.value = '';
                    textarea.style.height = '40px';
                    $('#sendCommentBtn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                    
                    // Add comment to DOM
                    const comment = response.data.comment;
                    // Format comment data to match renderComment expectations
                    const formattedComment = {
                        id: comment.id,
                        content: comment.content || '',
                        user: comment.user ? {
                            id: comment.user.id,
                            name: comment.user.name || '',
                            profile_picture: comment.user.profile_picture || '',
                            is_online: comment.user.is_online || false
                        } : {
                            id: '',
                            name: '',
                            profile_picture: '',
                            is_online: false
                        },
                        created_at: comment.created_at_human || comment.created_at || 'just now',
                        replies: comment.replies || []
                    };
                    const commentHtml = renderComment(formattedComment);
                        
                    // Remove "No comments yet" message if it exists
                    $('#commentsList').find('.text-center').remove();
                    
                    // Append new comment to the bottom of the list
                    $('#commentsList').append(commentHtml);
                    
                    // Update comment count
                    const $commentsCount = $('#commentsCount');
                    const currentCount = parseInt($commentsCount.text()) || 0;
                    $commentsCount.text(currentCount + 1);
                    
                    // Update "View More Comments" button if it exists
                    const $viewMoreBtn = $('#viewMoreCommentsBtn');
                    if ($viewMoreBtn.length) {
                        const loaded = parseInt($viewMoreBtn.data('loaded')) || 0;
                        const total = parseInt($viewMoreBtn.data('total')) || 0;
                        $viewMoreBtn.data('loaded', loaded + 1);
                        $viewMoreBtn.data('total', total + 1);
                        $viewMoreBtn.html(`View More Comments <span class="ml-2 text-gray-500 dark:text-gray-400">${loaded + 1} of ${total + 1}</span>`);
                    }
                    
                    // Check truncation for new comment
                    setTimeout(function() {
                        checkAndTruncateComments();
                    }, 100);
                    
                    // Update real-time tracking for our own comment
                    if (comment.id) {
                        seenCommentIds.add(comment.id);
                        if (comment.id > lastCommentId) {
                            lastCommentId = comment.id;
                        }
                        if (comment.created_at) {
                            lastTimestamp = comment.created_at;
                        }
                    }
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to post comment. Please try again.'
                    });
                }
        }

        // Comment submission - Enter submits, Shift+Enter creates new line
        $('#commentInput').on('keydown', async function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const content = $(this).val().trim();
                if (content.length > 0) {
                    await submitComment();
                }
            }
            // If Shift+Enter, allow default behavior (new line) - no preventDefault
        });
        
        // Send button click
        $('#sendCommentBtn').on('click', async function() {
            await submitComment();
        });

        // Edit comment button click
        $(document).on('click', '.edit-comment-btn', function() {
            const commentId = $(this).data('comment-id');
            const $commentElement = $(this).closest('.fb-comment');
            const $editContainer = $commentElement.find('.edit-comment-container');
            const $textWrapper = $commentElement.find('.fb-comment-text-wrapper');
            const $actions = $commentElement.find('.fb-comment-actions');
            
            // Hide text and actions, show edit container
            $textWrapper.addClass('hidden');
            $actions.addClass('hidden');
            $editContainer.removeClass('hidden');
            
            // Focus on textarea and auto-resize
            const $textarea = $editContainer.find('.edit-comment-textarea');
            $textarea.focus();
            $textarea[0].style.height = 'auto';
            $textarea[0].style.height = $textarea[0].scrollHeight + 'px';
        });
        
        // Cancel edit
        $(document).on('click', '.cancel-edit-btn', function() {
            const $editContainer = $(this).closest('.edit-comment-container');
            const $commentElement = $editContainer.closest('.fb-comment');
            const $textWrapper = $commentElement.find('.fb-comment-text-wrapper');
            const $actions = $commentElement.find('.fb-comment-actions');
            
            // Show text and actions, hide edit container
            $textWrapper.removeClass('hidden');
            $actions.removeClass('hidden');
            $editContainer.addClass('hidden');
        });
        
        // Save edit
        $(document).on('click', '.save-edit-btn', async function() {
            const commentId = $(this).data('comment-id');
            const $editContainer = $(this).closest('.edit-comment-container');
            const $textarea = $editContainer.find('.edit-comment-textarea');
            const content = $textarea.val().trim();
            
            if (!content) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Comment',
                    text: 'Comment cannot be empty.'
                });
                return;
            }
            
            if (isExpired) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Expired',
                    text: 'This referendum has expired. Comments cannot be edited.'
                });
                return;
            }
            
            try {
                const response = await axios.post(`/referendums/${referendumId}/comments/${commentId}`, {
                    content: content
                });
                
                if (response.data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Comment updated successfully!'
                    });
                    
                    // Update comment text
                    const $commentElement = $editContainer.closest('.fb-comment');
                    const $textElement = $commentElement.find('.fb-comment-text');
                    const $textWrapper = $commentElement.find('.fb-comment-text-wrapper');
                    const $actions = $commentElement.find('.fb-comment-actions');
                    
                    // Update content with line breaks preserved
                    const contentWithBreaks = content.replace(/\n/g, '<br>');
                    $textElement.html(contentWithBreaks);
                    $textElement.attr('data-full-text', escapeHtml(content));
                    
                    // Show text and actions, hide edit container
                    $textWrapper.removeClass('hidden');
                    $actions.removeClass('hidden');
                    $editContainer.addClass('hidden');
                    
                    // Re-check truncation
                    setTimeout(function() {
                        checkAndTruncateComments();
                    }, 100);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Failed to update comment. Please try again.'
                });
            }
        });
        
        // Auto-resize edit textarea
        $(document).on('input', '.edit-comment-textarea', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        // View/Hide Replies functionality (always enabled, even when expired)
        $(document).on('click', '.view-replies-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const $btn = $(this);
            const commentId = $btn.data('comment-id') || $btn.attr('data-comment-id');
            
            if (!commentId) {
                console.error('Comment ID not found for view replies button');
                return;
            }
            
            // Find the parent comment container
            const $parentComment = $btn.closest('.fb-comment');
            if ($parentComment.length === 0) {
                // Try finding by comment ID
                const $commentById = $('#comment-' + commentId);
                if ($commentById.length > 0) {
                    $parentComment = $commentById;
                } else {
                    console.error('Parent comment not found for ID:', commentId);
                    return;
                }
            }
            
            // Find the flex-1 container which contains the replies
            const $flexContainer = $parentComment.find('.flex-1').first();
            if ($flexContainer.length === 0) {
                console.error('Flex container not found');
                return;
            }
            
            // Find the replies container - it should be a sibling of the button container
            const $buttonContainer = $btn.closest('.mb-2, .mt-2');
            let $repliesContainer = $buttonContainer.siblings('.replies-container').first();
            
            // If not found as sibling, try finding within the flex container
            if ($repliesContainer.length === 0) {
                $repliesContainer = $flexContainer.find('.replies-container').first();
            }
            
            // If still not found, try finding in the parent comment
            if ($repliesContainer.length === 0) {
                $repliesContainer = $parentComment.find('.replies-container').first();
            }
            
            if ($repliesContainer.length > 0) {
                $repliesContainer.removeClass('hidden');
                $btn.addClass('hidden');
                $buttonContainer.find('.hide-replies-btn').removeClass('hidden');
                
                // Check and truncate replies after showing them
                setTimeout(function() {
                    checkAndTruncateComments();
                }, 100);
            } else {
                console.error('Replies container not found for comment ID:', commentId);
            }
        });
        
        $(document).on('click', '.hide-replies-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const $btn = $(this);
            const commentId = $btn.data('comment-id') || $btn.attr('data-comment-id');
            
            if (!commentId) {
                console.error('Comment ID not found for hide replies button');
                return;
            }
            
            // Find the parent comment container
            const $parentComment = $btn.closest('.fb-comment');
            if ($parentComment.length === 0) {
                // Try finding by comment ID
                const $commentById = $('#comment-' + commentId);
                if ($commentById.length > 0) {
                    $parentComment = $commentById;
                } else {
                    console.error('Parent comment not found for ID:', commentId);
                    return;
                }
            }
            
            // Find the button container
            const $buttonContainer = $btn.closest('.mb-2, .mt-2');
            
            // Find the replies container - it should be a sibling of the button container
            let $repliesContainer = $buttonContainer.siblings('.replies-container').first();
            
            // If not found as sibling, try finding within the parent comment
            if ($repliesContainer.length === 0) {
                $repliesContainer = $parentComment.find('.replies-container').first();
            }
            
            if ($repliesContainer.length > 0) {
                $repliesContainer.addClass('hidden');
                $btn.addClass('hidden');
                $buttonContainer.find('.view-replies-btn').removeClass('hidden');
            } else {
                console.error('Replies container not found for comment ID:', commentId);
            }
        });

        // Reply button click
        $(document).on('click', '.reply-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const commentId = $(this).data('comment-id');
            const $btn = $(this);
            const $commentElement = $btn.closest('.fb-comment');
            
            // Only find reply input that is a direct sibling of the comment actions, not nested ones
            // Find the comment actions container first
            const $commentActions = $btn.closest('.fb-comment-actions');
            if ($commentActions.length === 0) {
                return;
            }
            
            // Find reply input that is immediately after the comment actions (direct sibling)
            let replyInput = $commentActions.next('.fb-reply-input');
            
            // If not found as next sibling, try finding within the same comment but not in nested replies
            if (replyInput.length === 0) {
                // Find all reply inputs in this comment
                const allReplyInputs = $commentElement.find('.fb-reply-input');
                // Filter to only get the one that's not inside a replies-container (nested replies)
                replyInput = allReplyInputs.filter(function() {
                    return $(this).closest('.replies-container').length === 0;
                }).first();
            }
            
            @php
                $currentUserProfileMedia = Auth::user()->profile_picture ? \App\Models\MediaLibrary::find(Auth::user()->profile_picture) : null;
                $currentUserProfileUrl = $currentUserProfileMedia ? asset('storage/' . $currentUserProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) . '&size=150&background=1877f2&color=fff';
            @endphp
            
            // Check if reply input exists
            if (replyInput.length > 0) {
                // If it exists, toggle visibility
                if (replyInput.hasClass('hidden') || replyInput.is(':hidden')) {
                    // Show it - remove all hiding methods
                    replyInput.removeClass('hidden');
                    replyInput.css({
                        'display': '',
                        'visibility': ''
                    });
                    replyInput.show();
                    const $replyTextarea = replyInput.find('.reply-textarea');
                    if ($replyTextarea.length > 0) {
                        $replyTextarea.focus();
                        autoResizeTextarea($replyTextarea[0]);
                }
            } else {
                    // Hide it
                    replyInput.addClass('hidden');
                }
            } else {
                // Create new reply input if it doesn't exist
                const replyHtml = `
                    <div class="fb-comment-input fb-reply-input mt-2">
                        <img src="{{ $currentUserProfileUrl }}" alt="Your profile" class="fb-comment-avatar">
                        <div class="flex-1 relative">
                        <textarea 
                            rows="1" 
                            placeholder="Write a reply..."
                                class="fb-comment-input-box reply-textarea w-full"
                            data-parent-id="${commentId}"
                        ></textarea>
                            <div class="absolute right-2 bottom-2 flex items-center gap-1">
                                <button type="button" class="cancel-reply-btn p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded-full transition-all cursor-pointer z-10" title="Cancel" data-comment-id="${commentId}" onclick="$(this).closest(\'.fb-reply-input, .fb-comment-input\').addClass(\'hidden\').hide(); $(this).closest(\'.fb-reply-input, .fb-comment-input\').find(\'.reply-textarea\').val(\'\').css(\'height\', \'auto\'); $(this).closest(\'.fb-reply-input, .fb-comment-input\').find(\'.send-reply-btn\').prop(\'disabled\', true).addClass(\'opacity-50 cursor-not-allowed\'); return false;">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                                <button type="button" class="reply-emoji-btn chat-emoji-btn p-2 text-yellow-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition z-10" title="Add emoji">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button type="button" class="send-reply-btn p-1.5 text-[#1877f2] hover:bg-[#1877f2] hover:text-white rounded-full transition-all opacity-50 cursor-not-allowed z-10" disabled>
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                            <!-- Emoji Picker for Reply -->
                            <div class="reply-emoji-picker absolute bottom-12 right-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 hidden flex flex-col" style="max-height: 300px;">
                                @include('components.emoji-picker')
                            </div>
                        </div>
                    </div>
                `;
                const $replyHtml = $(replyHtml);
                // Insert the reply input immediately after the comment actions that was clicked
                $commentActions.after($replyHtml);
                
                // Store reference to the reply input on the cancel button for easy access
                $replyHtml.find('.cancel-reply-btn').data('reply-container', $replyHtml);
                
                const $replyTextarea = $replyHtml.find('.reply-textarea');
                if ($replyTextarea.length > 0) {
                    $replyTextarea.focus();
                    autoResizeTextarea($replyTextarea[0]);
                }
                
                // Initialize emoji picker for this reply input
                const $replyEmojiBtn = $replyHtml.find('.reply-emoji-btn');
                const $replyEmojiPicker = $replyHtml.find('.reply-emoji-picker');
                
                if ($replyEmojiBtn.length && $replyEmojiPicker.length) {
                    // Toggle emoji picker
                    $replyEmojiBtn.off('click').on('click', function(e) {
                        e.stopPropagation();
                        // Close all other emoji pickers
                        $('.reply-emoji-picker').not($replyEmojiPicker).addClass('hidden');
                        $replyEmojiPicker.toggleClass('hidden');
                    });
                    
                    // Emoji selection
                    $replyEmojiPicker.find('.emoji-item').off('click').on('click', function() {
                        const emoji = $(this).attr('data-emoji');
                        if ($replyTextarea.length > 0) {
                            const textarea = $replyTextarea[0];
                            const cursorPos = textarea.selectionStart;
                            const textBefore = textarea.value.substring(0, cursorPos);
                            const textAfter = textarea.value.substring(cursorPos);
                            textarea.value = textBefore + emoji + textAfter;
                            textarea.selectionStart = textarea.selectionEnd = cursorPos + emoji.length;
                            textarea.focus();
                            autoResizeTextarea(textarea);
                            
                            // Enable send button if content exists
                            const content = textarea.value.trim();
                            const sendBtn = $replyHtml.find('.send-reply-btn');
                            if (content.length > 0) {
                                sendBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                            }
                        }
                    });
                    
                    // Emoji category switching
                    $replyEmojiPicker.find('.emoji-category-btn').off('click').on('click', function() {
                        const category = $(this).attr('data-category');
                        const $categoryBtns = $replyEmojiPicker.find('.emoji-category-btn');
                        const $categoryDivs = $replyEmojiPicker.find('.emoji-category');
                        
                        // Update active button
                        $categoryBtns.removeClass('active');
                        $(this).addClass('active');
                        
                        // Show/hide category divs
                        $categoryDivs.each(function() {
                            if ($(this).attr('data-category') === category) {
                                $(this).removeClass('hidden').addClass('active');
                            } else {
                                $(this).addClass('hidden').removeClass('active');
            }
                        });
        });

                    // Emoji search functionality
                    const $emojiSearchInput = $replyEmojiPicker.find('.emoji-search-input');
                    if ($emojiSearchInput.length) {
                        $emojiSearchInput.off('input').on('input', function() {
                            const searchTerm = $(this).val().toLowerCase().trim();
                            const $emojiItems = $replyEmojiPicker.find('.emoji-item');
                            
                            if (searchTerm === '') {
                                $emojiItems.show();
                            } else {
                                $emojiItems.each(function() {
                                    const emoji = $(this).attr('data-emoji');
                                    $(this).show();
                                });
                            }
                        });
                    }
                }
            }
        });
        
        // Hide emoji pickers when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.reply-emoji-picker, .reply-emoji-btn').length) {
                $('.reply-emoji-picker').addClass('hidden');
            }
        });
        
        // Auto-resize and enable/disable send button for reply textareas
        $(document).on('input keyup', '.reply-textarea', function() {
            autoResizeTextarea(this);
            const content = $(this).val().trim();
            const sendReplyBtn = $(this).closest('.relative').find('.send-reply-btn');
            if (content.length > 0) {
                sendReplyBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            } else {
                sendReplyBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            }
        });
        
        // Cancel reply button - simplified and direct
        $(document).on('click', '.cancel-reply-btn', function(e) {
                e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            const $button = $(this);
            
            // Find the container - go up 3 levels: button -> div.absolute -> div.relative -> div.fb-reply-input
            let $container = $button.closest('.fb-reply-input');
            
            // If not found, try manual traversal
            if ($container.length === 0) {
                $container = $button.parent().parent().parent();
            }
            
            // Verify it has the right class
            if ($container.length === 0 || (!$container.hasClass('fb-reply-input') && !$container.hasClass('fb-comment-input'))) {
                // Try one more time with closest
                $container = $button.closest('.fb-comment-input, .fb-reply-input');
            }
            
            if ($container.length === 0) {
                console.error('Cancel: Container not found');
                return false;
            }
            
            // Clear textarea
            $container.find('.reply-textarea').val('').css('height', 'auto');
            
            // Disable send button
            $container.find('.send-reply-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            
            // Hide the reply input - use both class and display
            $container.addClass('hidden');
            $container.hide();
            
            return false;
        });
        
        // Also try mousedown as backup in case click is blocked
        $(document).on('mousedown', '.cancel-reply-btn', function(e) {
            if (e.button === 0) { // Left mouse button only
                $(this).trigger('click');
            }
        });
        
        // Submit reply on Enter (without Shift), allow new line on Shift+Enter
        $(document).on('keydown', '.reply-textarea', async function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const content = $(this).val().trim();
                if (content.length > 0) {
                    // Trigger send button click
                    $(this).closest('.relative').find('.send-reply-btn').click();
                }
            } else if (e.key === 'Enter' && e.shiftKey) {
                // Shift+Enter = new line, resize textarea
                setTimeout(() => {
                    autoResizeTextarea(this);
                }, 10);
            }
        });
        
        // Submit reply on send button click
        $(document).on('click', '.send-reply-btn', async function() {
            if (!$(this).prop('disabled')) {
                const $textarea = $(this).closest('.relative').find('.reply-textarea');
                const content = $textarea.val().trim();
                if (!content) {
                    return;
                }
                
                if (isExpired) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Expired',
                        text: 'This referendum has expired. Commenting is no longer available.'
                    });
                    return;
                }

                const parentId = $textarea.data('parent-id');
                if (!parentId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to determine parent comment. Please try again.'
                    });
                    return;
                }

                const $replyInput = $textarea.closest('.fb-reply-input');
                const $parentComment = $replyInput.closest('.fb-comment');

                try {
                    const response = await axios.post(`/referendums/${referendumId}/comments`, {
                        content: content,
                        parent_id: parentId
                    });

                    if (response.data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Reply posted successfully!'
                        });
                        
                        // Clear reply input
                        $textarea.val('');
                        $textarea.css('height', 'auto');
                        $replyInput.addClass('hidden');
                        $(this).prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                        
                        // Get the reply data
                        const reply = response.data.comment;
                        // Format reply data to match renderReply expectations
                        const formattedReply = {
                            id: reply.id,
                            content: reply.content || '',
                            user: reply.user ? {
                                id: reply.user.id,
                                name: reply.user.name || '',
                                profile_picture: reply.user.profile_picture || '',
                                is_online: reply.user.is_online || false
                            } : {
                                id: '',
                                name: '',
                                profile_picture: '',
                                is_online: false
                            },
                            created_at_human: reply.created_at_human || reply.created_at || 'just now',
                            created_at: reply.created_at_human || reply.created_at || 'just now',
                            replies: reply.replies || []
                        };
                        const replyHtml = renderReply(formattedReply);
                        
                        // Find the actual parent comment element by its data-comment-id
                        // This works for both main comments and nested replies
                        const $actualParentComment = $('[data-comment-id="' + parentId + '"]').first();
                        
                        if ($actualParentComment.length === 0) {
                            console.error('Parent comment not found for ID:', parentId);
                            return;
                        }
                        
                        // Find the parent's flex-1 container (where replies should be added)
                        const $parentFlexContainer = $actualParentComment.closest('.fb-comment').find('.flex-1').first();
                        
                        if ($parentFlexContainer.length === 0) {
                            console.error('Parent flex container not found');
                            return;
                        }
                        
                        // Check if this parent already has a replies container
                        let $repliesContainer = $parentFlexContainer.find('.replies-container').first();
                        let $viewRepliesBtn = $parentFlexContainer.find('.view-replies-btn[data-comment-id="' + parentId + '"]').first();
                        let $hideRepliesBtn = $parentFlexContainer.find('.hide-replies-btn[data-comment-id="' + parentId + '"]').first();
                        
                        if ($repliesContainer.length > 0) {
                            // Replies container exists - add to it
                            if ($repliesContainer.hasClass('hidden')) {
                                // Show replies container if hidden
                                $repliesContainer.removeClass('hidden');
                                if ($viewRepliesBtn.length) $viewRepliesBtn.addClass('hidden');
                                if ($hideRepliesBtn.length) $hideRepliesBtn.removeClass('hidden');
                            }
                            $repliesContainer.append(replyHtml);
                            
                            // Update "View all X replies" button text
                            const repliesCount = $repliesContainer.find('.fb-comment-reply').length;
                            const repliesText = repliesCount === 1 ? 'reply' : 'replies';
                            if ($viewRepliesBtn.length) {
                                $viewRepliesBtn.html('<i class="fas fa-chevron-down mr-1"></i>View all ' + repliesCount + ' ' + repliesText);
                            }
                        } else {
                            // No replies container yet - create it
                            // Find where to insert it (after the edit container or comment actions)
                            let $insertAfter = $parentFlexContainer.find('.edit-comment-container').last();
                            if ($insertAfter.length === 0) {
                                $insertAfter = $parentFlexContainer.find('.fb-comment-actions').last();
                            }
                            
                            const repliesHtml = '<div class="mt-2">' +
                                '<div class="mb-2">' +
                                '<button type="button" class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer hidden" data-comment-id="' + parentId + '">' +
                                '<i class="fas fa-chevron-down mr-1"></i>View all 1 reply' +
                                '</button>' +
                                '<button type="button" class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer" data-comment-id="' + parentId + '">' +
                                '<i class="fas fa-chevron-up mr-1"></i>Hide replies' +
                                '</button>' +
                                '</div>' +
                                '<div class="replies-container mt-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700">' +
                                replyHtml +
                                '</div>' +
                                '</div>';
                            
                            if ($insertAfter.length > 0) {
                                $insertAfter.after(repliesHtml);
                            } else {
                                // Last resort: append to the flex container
                                $parentFlexContainer.append(repliesHtml);
                            }
                        }
                        
                        // Update comment count (including nested replies)
                        const $commentsCount = $('#commentsCount');
                        const currentCount = parseInt($commentsCount.text()) || 0;
                        $commentsCount.text(currentCount + 1);
                        
                        // Check truncation for new reply
                        setTimeout(function() {
                            checkAndTruncateComments();
                        }, 100);
                        
                        // Update real-time tracking for our own reply
                        if (reply.id) {
                            seenCommentIds.add(reply.id);
                            if (reply.id > lastCommentId) {
                                lastCommentId = reply.id;
                            }
                            if (reply.created_at) {
                                lastTimestamp = reply.created_at;
                            }
                        }
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to post reply. Please try again.'
                    });
                }
            }
        });


        // Auto-resize reply textarea
        $(document).on('input', '.reply-textarea', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Delete comment
        $(document).on('click', '.delete-comment-btn', async function() {
            const commentId = $(this).data('comment-id');
            if (!commentId) {
                console.error('Comment ID not found');
                return;
            }
            
            // Find the comment element by data-comment-id attribute
            const $commentWithId = $('[data-comment-id="' + commentId + '"]');
            if ($commentWithId.length === 0) {
                console.error('Comment element not found for ID:', commentId);
                return;
            }
            
            // Find the wrapper element (.fb-comment-reply for replies, or the .fb-comment itself for main comments)
            let $commentElement = $commentWithId.closest('.fb-comment-reply');
            if ($commentElement.length === 0) {
                // It's a main comment, find the parent .fb-comment container
                $commentElement = $commentWithId.closest('.fb-comment').parent();
                if ($commentElement.length === 0) {
                    $commentElement = $commentWithId.closest('.fb-comment');
                }
            }
            
            // Check if it's a main comment (not inside a replies-container)
            const isMainComment = $commentElement.closest('#commentsList > .fb-comment, #commentsList > div > .fb-comment').length > 0 && 
                                  $commentElement.closest('.replies-container').length === 0;
            
            const result = await Swal.fire({
                title: 'Delete Comment?',
                text: 'Are you sure you want to delete this comment? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    const response = await axios.delete(`/referendums/${referendumId}/comments/${commentId}`);

                    if (response.data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Comment deleted successfully!'
                        });
                        
                        // Remove comment from DOM
                        $commentElement.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update comment count (including nested replies)
                            const $commentsCount = $('#commentsCount');
                            const currentCount = parseInt($commentsCount.text()) || 0;
                            const newCount = Math.max(0, currentCount - 1);
                            $commentsCount.text(newCount);
                            
                            // Update "View More Comments" button only if it's a main comment
                            if (isMainComment) {
                                // Update "View More Comments" button
                                const $viewMoreBtn = $('#viewMoreCommentsBtn');
                                if ($viewMoreBtn.length) {
                                    const loaded = parseInt($viewMoreBtn.data('loaded')) || 0;
                                    const total = parseInt($viewMoreBtn.data('total')) || 0;
                                    const newTotal = Math.max(0, total - 1);
                                    const newLoaded = Math.max(0, loaded - 1);
                                    
                                    $viewMoreBtn.data('total', newTotal);
                                    $viewMoreBtn.data('loaded', newLoaded);
                                    
                                    if (newTotal > newLoaded) {
                                        $viewMoreBtn.html(`View More Comments <span class="ml-2 text-gray-500 dark:text-gray-400">${newLoaded} of ${newTotal}</span>`);
                                    } else {
                                        $viewMoreBtn.closest('div').remove();
                                    }
                                }
                            }
                            
                            // Update reply counts for parent comments
                            const $parentRepliesContainer = $commentElement.closest('.replies-container');
                            if ($parentRepliesContainer.length > 0) {
                                const $parentComment = $parentRepliesContainer.closest('.fb-comment');
                                const $viewRepliesBtn = $parentComment.find('.view-replies-btn');
                                const $hideRepliesBtn = $parentComment.find('.hide-replies-btn');
                                const remainingReplies = $parentRepliesContainer.find('.fb-comment-reply').length;
                                
                                if (remainingReplies === 0) {
                                    // No more replies, hide the replies container and show "View replies" button
                                    $parentRepliesContainer.addClass('hidden');
                                    $viewRepliesBtn.removeClass('hidden');
                                    $hideRepliesBtn.addClass('hidden');
                                    $viewRepliesBtn.html('<i class="fas fa-chevron-down mr-1"></i>View all 0 replies');
                                } else {
                                    // Update button text
                                    const repliesText = remainingReplies === 1 ? 'reply' : 'replies';
                                    if ($viewRepliesBtn.hasClass('hidden')) {
                                        $hideRepliesBtn.html('<i class="fas fa-chevron-up mr-1"></i>Hide replies');
                                    } else {
                                        $viewRepliesBtn.html('<i class="fas fa-chevron-down mr-1"></i>View all ' + remainingReplies + ' ' + repliesText);
                                    }
                                }
                            }
                            
                            // Hide comments section if no comments left
                            if (isMainComment && newCount === 0) {
                                $('#commentsList').html(`
                                    <div class="text-center py-2">
                                        <i class="fas fa-comment-slash text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No comments yet.</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Be the first to comment!</p>
                                    </div>
                                `);
                            }
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to delete comment. Please try again.'
                    });
                }
            }
        });
        
        // View More Comments
        $(document).on('click', '#viewMoreCommentsBtn', async function() {
            const $btn = $(this);
            const loaded = parseInt($btn.data('loaded')) || 0;
            const total = parseInt($btn.data('total')) || 0;
            const offset = loaded;
            
            try {
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...');
                
                const response = await axios.get(`/referendums/${referendumId}/comments`, {
                    params: {
                        offset: offset,
                        limit: 5
                    }
                });
                
                if (response.data.success && response.data.comments.length > 0) {
                    // Render and append new comments
                    response.data.comments.forEach(function(comment) {
                        const commentHtml = renderComment(comment);
                        $('#commentsList').append(commentHtml);
                    });
                    
                    // Check and truncate newly loaded comments
                    setTimeout(function() {
                        checkAndTruncateComments();
                        
                        // Check if we need to scroll to a comment after loading more
                        const hash = window.location.hash;
                        if (hash && hash.startsWith('#comment-')) {
                            const commentId = hash.replace('#comment-', '');
                            scrollToComment(commentId);
                        }
                    }, 100);
                    
                    // Update button state
                    const newLoaded = response.data.loaded;
                    const newTotal = response.data.total;
                    
                    if (newLoaded < newTotal) {
                        $btn.data('loaded', newLoaded);
                        $btn.data('total', newTotal);
                        $btn.html(`View More Comments <span class="ml-2 text-gray-500 dark:text-gray-400">${newLoaded} of ${newTotal}</span>`);
                        $btn.prop('disabled', false);
                    } else {
                        $btn.closest('div').remove();
                    }
                } else {
                    $btn.closest('div').remove();
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load more comments. Please try again.'
                });
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
        
        // Function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Function to render a comment
        function renderComment(comment) {
            if (!comment || !comment.user) {
                console.error('Invalid comment object:', comment);
                return '';
            }
            
            const userId = '{{ Auth::id() }}';
            const canDelete = comment.user && comment.user.id === userId;
            
            let repliesHtml = '';
            if (comment.replies && comment.replies.length > 0) {
                const repliesCount = comment.replies.length;
                const repliesText = repliesCount === 1 ? 'reply' : 'replies';
                repliesHtml = '<div class="mt-2">' +
                    '<div class="mb-2">' +
                    '<button type="button" class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer" data-comment-id="' + comment.id + '">' +
                    '<i class="fas fa-chevron-down mr-1"></i>' +
                    'View all ' + repliesCount + ' ' + repliesText +
                    '</button>' +
                    '<button type="button" class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer hidden" data-comment-id="' + comment.id + '">' +
                    '<i class="fas fa-chevron-up mr-1"></i>' +
                    'Hide replies' +
                    '</button>' +
                    '</div>' +
                    '<div class="replies-container hidden mt-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700">';
                comment.replies.forEach(function(reply) {
                    // Use renderReply function which handles nested replies recursively
                    repliesHtml += renderReply(reply);
                });
                repliesHtml += '</div>' + // Close replies-container
                    '</div>'; // Close mt-2 wrapper
            }
            
            const commentContent = escapeHtml(comment.content || '').replace(/\n/g, '<br>');
            const commentName = escapeHtml(comment.user.name || '');
            const commentProfilePic = escapeHtml(comment.user.profile_picture || '');
            // Format date: if less than 20 seconds old, show "just now"
            let commentCreatedAt = '';
            if (comment.created_at_human) {
                commentCreatedAt = comment.created_at_human;
            } else if (comment.created_at) {
                // Parse the date and check if it's less than 20 seconds old
                const commentDate = new Date(comment.created_at);
                const now = new Date();
                const secondsDiff = Math.floor((now - commentDate) / 1000);
                commentCreatedAt = secondsDiff < 20 ? 'Just Now' : comment.created_at;
            } else {
                commentCreatedAt = 'Just Now';
            }
            commentCreatedAt = escapeHtml(commentCreatedAt);
            const canEdit = comment.user && comment.user.id === userId && !isExpired;
            const isCommenterOnline = (comment.user.is_online || false);
            const commenterOnlineClass = isCommenterOnline ? 'online-indicator' : 'offline-indicator';
            @php
                $currentUserProfileMedia = Auth::user()->profile_picture ? \App\Models\MediaLibrary::find(Auth::user()->profile_picture) : null;
                $currentUserProfileUrl = $currentUserProfileMedia ? asset('storage/' . $currentUserProfileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name . ' ' . Auth::user()->last_name) . '&size=150&background=1877f2&color=fff';
            @endphp
            const currentUserProfileUrl = '{{ $currentUserProfileUrl }}';
            
            return '<div class="fb-comment" id="comment-' + comment.id + '" data-comment-id="' + comment.id + '">' +
                '<div class="profile-picture-container">' +
                '<img src="' + commentProfilePic + '" alt="' + commentName + '" class="fb-comment-avatar">' +
                '<div class="' + commenterOnlineClass + '"></div>' +
                '</div>' +
                '<div class="flex-1">' +
                '<div class="fb-comment-content">' +
                '<div class="fb-comment-author">' + commentName + '</div>' +
                '<div class="fb-comment-text-wrapper">' +
                '<div class="fb-comment-text" data-full-text="' + escapeHtml(comment.content || '') + '">' + commentContent + '</div>' +
                '<button type="button" class="see-more-btn hidden text-xs font-semibold text-[#1877f2] hover:underline mt-1 cursor-pointer">See more</button>' +
                '<button type="button" class="see-less-btn hidden text-xs font-semibold text-[#1877f2] hover:underline mt-1 cursor-pointer">See less</button>' +
                '</div>' +
                '</div>' +
                '<div class="fb-comment-actions">' +
                (!isExpired ? '<span class="fb-comment-action reply-btn" data-comment-id="' + comment.id + '">Reply</span>' : '') +
                (canEdit ? '<span class="fb-comment-action edit-comment-btn text-[#1877f2]" data-comment-id="' + comment.id + '">Edit</span>' : '') +
                (canDelete ? '<span class="fb-comment-action delete-comment-btn text-red-500" data-comment-id="' + comment.id + '">Delete</span>' : '') +
                '<span class="text-xs text-gray-500 dark:text-gray-400">' + commentCreatedAt + '</span>' +
                '</div>' +
                (canEdit ? '<div class="edit-comment-container hidden mt-2">' +
                    '<div class="flex items-start space-x-2">' +
                    '<img src="' + currentUserProfileUrl + '" alt="Your profile" class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-1">' +
                    '<div class="flex-1">' +
                    '<textarea class="edit-comment-textarea w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white resize-none focus:outline-none focus:ring-2 focus:ring-[#1877f2] focus:border-[#1877f2] transition-all" rows="2" data-comment-id="' + comment.id + '">' + (comment.content || '').replace(/<br>/g, '\n') + '</textarea>' +
                    '<div class="flex items-center justify-end gap-2 mt-2">' +
                    '<button type="button" class="cancel-edit-btn text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-semibold">Cancel</button>' +
                    '<button type="button" class="save-edit-btn text-xs bg-[#1877f2] text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-[#166fe5] transition-colors" data-comment-id="' + comment.id + '">Save</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' : '') +
                repliesHtml +
                '</div>' +
                '</div>';
        }
        
        // Check comments after loading more
        $(document).on('DOMNodeInserted', '#commentsList', function() {
            setTimeout(function() {
                checkAndTruncateComments();
            }, 100);
        });
    </script>
    
    @include('components.footer')
    
    <!-- Global PDF Modal - Available on all pages -->
    @include('components.pdf-modal')

    <!-- Image Gallery Modal -->
    <div id="imageGalleryModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center">
        <div class="relative w-full h-full flex items-center justify-center">
            <!-- Close Button -->
            <button 
                onclick="closeImageGallery()" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 p-2"
                style="background: rgba(0,0,0,0.5); border-radius: 50%;"
            >
                <i class="fas fa-times text-2xl"></i>
            </button>

            <!-- Previous Button -->
            <button 
                onclick="previousImage()" 
                id="prevImageBtn"
                class="absolute left-4 text-white hover:text-gray-300 z-10 p-3 rounded-full transition hidden"
                style="background: rgba(0,0,0,0.5);"
            >
                <i class="fas fa-chevron-left text-2xl"></i>
            </button>

            <!-- Next Button -->
            <button 
                onclick="nextImage()" 
                id="nextImageBtn"
                class="absolute right-4 text-white hover:text-gray-300 z-10 p-3 rounded-full transition hidden"
                style="background: rgba(0,0,0,0.5);"
            >
                <i class="fas fa-chevron-right text-2xl"></i>
            </button>

            <!-- Image Container -->
            <div class="flex items-center justify-center w-full h-full p-4">
                <img 
                    id="galleryImage" 
                    src="" 
                    alt="Gallery Image" 
                    class="max-w-full max-h-full object-contain"
                >
            </div>

            <!-- Image Counter -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm px-4 py-2 rounded-full" style="background: rgba(0,0,0,0.5);">
                <span id="imageCounter">1 / 1</span>
            </div>
        </div>
    </div>

    <script>
        // Attachment Gallery Data (Images and PDFs)
        @php
            $galleryData = [];
            if(isset($previewAttachments) && is_array($previewAttachments)) {
                foreach($previewAttachments as $media) {
                    $isImage = Str::startsWith($media->file_type, 'image/');
                    $galleryData[] = [
                        'url' => asset('storage/' . $media->file_path),
                        'name' => $media->file_name,
                        'isImage' => $isImage
                    ];
                }
            }
        @endphp
        const attachmentGalleryData = @json($galleryData);
        
        let currentImageIndex = 0;

        // Open Image Gallery (for backward compatibility)
        function openImageGallery(index) {
            openAttachmentGallery(index);
        }

        // Open Attachment Gallery (Images and PDFs)
        function openAttachmentGallery(index) {
            if (attachmentGalleryData.length === 0) return;
            
            currentImageIndex = index;
            const item = attachmentGalleryData[currentImageIndex];
            
            // If it's a PDF, open in PDF modal
            if (!item.isImage) {
                openGlobalPdfModal(item.url, item.name);
                return;
            }
            
            // Otherwise, open in image gallery
            const modal = document.getElementById('imageGalleryModal');
            const galleryImage = document.getElementById('galleryImage');
            const imageCounter = document.getElementById('imageCounter');
            const prevBtn = document.getElementById('prevImageBtn');
            const nextBtn = document.getElementById('nextImageBtn');

            // Show/hide navigation buttons
            const imageCount = attachmentGalleryData.filter(item => item.isImage).length;
            if (imageCount > 1) {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            }

            updateGalleryImage();
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Update Gallery Image
        function updateGalleryImage() {
            const galleryImage = document.getElementById('galleryImage');
            const imageCounter = document.getElementById('imageCounter');
            
            // Find next/previous image (skip PDFs)
            let imageItems = attachmentGalleryData.filter(item => item.isImage);
            let imageIndex = 0;
            for (let i = 0; i <= currentImageIndex; i++) {
                if (attachmentGalleryData[i].isImage) {
                    imageIndex = imageItems.findIndex(item => item.url === attachmentGalleryData[i].url);
                }
            }
            
            if (imageItems[imageIndex]) {
                galleryImage.src = imageItems[imageIndex].url;
                galleryImage.alt = imageItems[imageIndex].name;
                imageCounter.textContent = `${imageIndex + 1} / ${imageItems.length}`;
            }
        }

        // Next Image (skip PDFs)
        function nextImage() {
            do {
                if (currentImageIndex < attachmentGalleryData.length - 1) {
                    currentImageIndex++;
                } else {
                    currentImageIndex = 0; // Loop to first
                }
            } while (!attachmentGalleryData[currentImageIndex].isImage && attachmentGalleryData.length > 1);
            
            if (attachmentGalleryData[currentImageIndex].isImage) {
                updateGalleryImage();
            } else {
                // If only PDFs, open PDF modal
                const item = attachmentGalleryData[currentImageIndex];
                closeImageGallery();
                openGlobalPdfModal(item.url, item.name);
            }
        }

        // Previous Image (skip PDFs)
        function previousImage() {
            do {
                if (currentImageIndex > 0) {
                    currentImageIndex--;
                } else {
                    currentImageIndex = attachmentGalleryData.length - 1; // Loop to last
                }
            } while (!attachmentGalleryData[currentImageIndex].isImage && attachmentGalleryData.length > 1);
            
            if (attachmentGalleryData[currentImageIndex].isImage) {
                updateGalleryImage();
            } else {
                // If only PDFs, open PDF modal
                const item = attachmentGalleryData[currentImageIndex];
                closeImageGallery();
                openGlobalPdfModal(item.url, item.name);
            }
        }

        // Close Image Gallery
        function closeImageGallery() {
            const modal = document.getElementById('imageGalleryModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageGalleryModal');
            if (!modal.classList.contains('hidden')) {
                if (e.key === 'ArrowLeft') {
                    previousImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                } else if (e.key === 'Escape') {
                    closeImageGallery();
                }
            }
        });

        // Close on outside click
        document.getElementById('imageGalleryModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageGallery();
            }
        });

        // ========== REAL-TIME COMMENT UPDATES ==========
        let lastCommentId = 0;
        let lastTimestamp = null;
        let realTimeUpdateInterval = null;
        const seenCommentIds = new Set();

        // Initialize: Get the last comment ID from the page
        function initializeRealTimeUpdates() {
            // Get all comment IDs currently on the page
            $('[data-comment-id]').each(function() {
                const commentId = parseInt($(this).data('comment-id'));
                if (commentId && !isNaN(commentId)) {
                    seenCommentIds.add(commentId);
                    if (commentId > lastCommentId) {
                        lastCommentId = commentId;
                    }
                }
            });

            // Get the latest timestamp from existing comments
            const $lastComment = $('[data-comment-id]').last();
            if ($lastComment.length > 0) {
                // Try to get timestamp from the comment element or use current time
                lastTimestamp = new Date().toISOString();
            }

            // Start polling for new comments every 3 seconds
            realTimeUpdateInterval = setInterval(checkForNewComments, 3000);
        }

        // Check for new comments
        async function checkForNewComments() {
            try {
                const params = {};
                if (lastCommentId > 0) {
                    params.last_comment_id = lastCommentId;
                } else if (lastTimestamp) {
                    params.last_timestamp = lastTimestamp;
                }

                const response = await axios.get(`/referendums/${referendumId}/comments/new`, { params });

                if (response.data.success) {
                    const { main_comments, replies, last_comment_id, last_timestamp } = response.data;

                    // Process new main comments
                    if (main_comments && main_comments.length > 0) {
                        main_comments.forEach(function(comment) {
                            if (!seenCommentIds.has(comment.id)) {
                                seenCommentIds.add(comment.id);
                                appendNewMainComment(comment);
                                
                        
                            }
                        });
                    }

                    // Process new replies
                    if (replies && replies.length > 0) {
                        replies.forEach(function(reply) {
                            if (!seenCommentIds.has(reply.id)) {
                                seenCommentIds.add(reply.id);
                                appendNewReply(reply);
                            }
                        });
                    }

                    // Update last comment ID and timestamp
                    if (last_comment_id && last_comment_id > lastCommentId) {
                        lastCommentId = last_comment_id;
                    }
                    if (last_timestamp) {
                        lastTimestamp = last_timestamp;
                    }
                }
            } catch (error) {
                // Silently fail - don't spam console with errors
                if (error.response && error.response.status !== 403) {
                    console.error('Error checking for new comments:', error);
                }
            }
        }

        // Append a new main comment to the comments list
        function appendNewMainComment(comment) {
            const commentHtml = renderComment(comment);
            const $commentsList = $('#commentsList');
            
            if ($commentsList.length === 0) {
                // If comments list doesn't exist, create it
                const $commentsSection = $('.fb-comments-section');
                if ($commentsSection.length > 0) {
                    $commentsSection.append('<div id="commentsList" class="space-y-4"></div>');
                } else {
                    return; // Can't find where to append
                }
            }

            // Append the new comment
            $('#commentsList').append(commentHtml);

            // Check and truncate if needed
            setTimeout(function() {
                checkAndTruncateComments();
            }, 100);

            // Smooth scroll to the new comment (only if user is near the bottom of the page)
            const scrollPosition = window.innerHeight + window.scrollY;
            const documentHeight = document.documentElement.scrollHeight;
            const distanceFromBottom = documentHeight - scrollPosition;

            if (distanceFromBottom < 500) {
                // User is near the bottom, scroll to new comment
                setTimeout(function() {
                    const $newComment = $('#comment-' + comment.id);
                    if ($newComment.length > 0) {
                        $newComment[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        // Highlight the new comment briefly
                        $newComment.css('background-color', 'rgba(24, 119, 242, 0.1)');
                        setTimeout(function() {
                            $newComment.css('background-color', '');
                        }, 2000);
                    }
                }, 100);
            }
        }

        // Append a new reply to its parent comment's replies container
        function appendNewReply(reply) {
            if (!reply.parent_id) {
                return; // This shouldn't happen, but just in case
            }

            const $parentComment = $('#comment-' + reply.parent_id);
            if ($parentComment.length === 0) {
                return; // Parent comment not found
            }

            // Find or create the replies container
            let $repliesContainer = $parentComment.find('.replies-container').first();
            
            if ($repliesContainer.length === 0) {
                // Create replies container if it doesn't exist
                const $parentContent = $parentComment.find('.flex-1').first();
                if ($parentContent.length > 0) {
                    // Check if there's already a "View replies" button
                    let $viewRepliesBtn = $parentComment.find('.view-replies-btn[data-comment-id="' + reply.parent_id + '"]');
                    
                    if ($viewRepliesBtn.length === 0) {
                        // Create the view replies button and container
                        const repliesCount = 1;
                        const repliesText = repliesCount === 1 ? 'reply' : 'replies';
                        const viewRepliesHtml = '<div class="mt-2">' +
                            '<div class="mb-2">' +
                            '<button type="button" class="view-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer" data-comment-id="' + reply.parent_id + '">' +
                            '<i class="fas fa-chevron-down mr-1"></i>' +
                            'View all ' + repliesCount + ' ' + repliesText +
                            '</button>' +
                            '<button type="button" class="hide-replies-btn text-xs font-semibold text-[#1877f2] hover:underline cursor-pointer hidden" data-comment-id="' + reply.parent_id + '">' +
                            '<i class="fas fa-chevron-up mr-1"></i>' +
                            'Hide replies' +
                            '</button>' +
                            '</div>' +
                            '<div class="replies-container mt-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700"></div>' +
                            '</div>';
                        $parentContent.append(viewRepliesHtml);
                        $repliesContainer = $parentComment.find('.replies-container').first();
                    } else {
                        // View replies button exists, create container
                        $viewRepliesBtn.after('<div class="replies-container hidden mt-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700"></div>');
                        $repliesContainer = $parentComment.find('.replies-container').first();
                    }
                } else {
                    return; // Can't find parent content area
                }
            }

            // Make sure replies container is visible
            $repliesContainer.removeClass('hidden');
            $parentComment.find('.view-replies-btn[data-comment-id="' + reply.parent_id + '"]').addClass('hidden');
            $parentComment.find('.hide-replies-btn[data-comment-id="' + reply.parent_id + '"]').removeClass('hidden');

            // Render and append the reply
            const replyHtml = renderReply(reply);
            $repliesContainer.append(replyHtml);

            // Check and truncate if needed
            setTimeout(function() {
                checkAndTruncateComments();
            }, 100);
        }

        // Initialize real-time updates when page is ready
        $(document).ready(function() {
            // Wait a bit for initial comments to render
            setTimeout(function() {
                initializeRealTimeUpdates();
            }, 1000);

            // Pause polling when page is hidden, resume when visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (realTimeUpdateInterval) {
                        clearInterval(realTimeUpdateInterval);
                        realTimeUpdateInterval = null;
                    }
                } else {
                    if (!realTimeUpdateInterval) {
                        realTimeUpdateInterval = setInterval(checkForNewComments, 3000);
                    }
                }
            });
        });
    </script>
</body>
</html>
