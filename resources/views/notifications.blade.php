<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Notifications - Board Member Portal</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="shortcut icon" type="image/png" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <link rel="apple-touch-icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Dangerous_Drugs_Board_%28DDB%29.svg/1209px-Dangerous_Drugs_Board_%28DDB%29.svg.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
        .notification-item {
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            transform: translateY(-2px);
        }
        .notification-menu-dropdown {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .notification-menu-dropdown button {
            cursor: pointer;
        }
        .notification-menu-dropdown button:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')
    
    <!-- Notifications Content -->
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-2">
                            Notifications
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">Stay updated with all board activities</p>
                    </div>
                    @if($notifications->where('is_read', false)->count() > 0)
                        <button id="markAllReadBtn" class="w-full sm:w-auto px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            <i class="fas fa-check-double mr-2"></i>Mark all as read
                    </button>
                    @endif
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-4">
                    <!-- Status Filter -->
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                        <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-0">Status:</span>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                               class="px-3 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'all' ? 'text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}" 
                               style="{{ $filter === 'all' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                    All
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                               class="px-3 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'unread' ? 'text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}" 
                               style="{{ $filter === 'unread' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                    Unread
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
                               class="px-3 py-1.5 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'read' ? 'text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}" 
                               style="{{ $filter === 'read' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                                Read
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="space-y-4">
                @forelse($notifications as $notification)
                    @php
                        $iconMap = [
                            'pending_registration' => ['icon' => 'fa-clock', 'color' => '#055498'],
                            'announcement' => ['icon' => 'fa-bullhorn', 'color' => '#055498'],
                            'notice' => ['icon' => 'fa-file-text', 'color' => '#055498'],
                            'referendum_comment' => ['icon' => 'fa-comment', 'color' => '#055498'],
                            'referendum_comment_reply' => ['icon' => 'fa-reply', 'color' => '#055498'],
                            'default' => ['icon' => 'fa-bell', 'color' => '#055498']
                        ];
                        $notificationType = $iconMap[$notification->type] ?? $iconMap['default'];
                        $borderColor = 'border-[#055498]';
                        $bgColor = 'bg-[#055498]/10';
                        $iconColor = 'text-[#055498]';
                    @endphp
                    <div class="notification-item bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-4 sm:p-6 border-l-4 {{ $borderColor }} {{ !$notification->is_read ? 'bg-[#055498]/5 dark:bg-[#055498]/10' : '' }} relative group" data-notification-id="{{ $notification->id }}">
                        <div class="flex items-start space-x-3 sm:space-x-4">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 {{ $bgColor }} dark:bg-[#055498]/20 rounded-full flex items-center justify-center">
                                <i class="fas {{ $notificationType['icon'] }} {{ $iconColor }} text-sm sm:text-base"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-1 sm:gap-0">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white {{ !$notification->is_read ? 'font-bold' : '' }}">
                                        {{ $notification->title }}
                                    </h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-3 break-words">{{ $notification->message }}</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-medium {{ $bgColor }} dark:bg-[#055498]/20 {{ $iconColor }} rounded">
                                        {{ ucwords(str_replace('_', ' ', $notification->type)) }}
                                    </span>
                                    @if(!$notification->is_read)
                                        <span class="px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(206, 32, 40, 0.1); color: #CE2028;">Unread</span>
                                    @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>
                                    @endif
                                    @if($notification->url)
                                        @if($notification->type === 'announcement' && str_contains($notification->url, '/announcements/'))
                                            <button onclick="openAnnouncementModalFromNotification('{{ $notification->url }}', {{ $notification->id }})" class="px-3 py-1 text-xs font-medium text-white rounded hover:opacity-90 transition whitespace-nowrap notification-link" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" data-notification-id="{{ $notification->id }}">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                        @else
                                            <a href="{{ $notification->url }}" class="px-3 py-1 text-xs font-medium text-white rounded hover:opacity-90 transition whitespace-nowrap notification-link" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);" data-notification-id="{{ $notification->id }}">
                                                <i class="fas fa-external-link-alt mr-1"></i>View
                                            </a>
                                        @endif
                                    @endif
                        </div>
                            </div>
                            @if(!$notification->is_read)
                                <div class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full"></div>
                            @endif
                            <div class="notification-menu-container relative flex-shrink-0 ml-2">
                                <button type="button" class="notification-menu-btn w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-opacity opacity-0 group-hover:opacity-100" data-notification-id="{{ $notification->id }}">
                                    <i class="fas fa-ellipsis-h text-gray-600 dark:text-gray-400 text-sm"></i>
                                </button>
                                <div class="notification-menu-dropdown absolute right-0 top-8 bg-gray-800 dark:bg-gray-700 rounded-lg shadow-xl border border-gray-700 dark:border-gray-600 z-50 hidden min-w-[180px]" data-menu-id="{{ $notification->id }}">
                                    <div class="py-1">
                                        @if(!$notification->is_read)
                                            <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="{{ $notification->id }}">
                                                <i class="fas fa-check text-sm"></i>
                                                Mark as read
                                            </button>
                                        @else
                                            <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="{{ $notification->id }}">
                                                <i class="fas fa-check text-sm"></i>
                                                Mark as unread
                                            </button>
                                        @endif
                                        <button type="button" class="notification-delete-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-times text-sm"></i>
                                            Delete this notification
                                        </button>
                    </div>
                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-8 sm:p-12 text-center">
                        <i class="fas fa-bell-slash text-gray-400 dark:text-gray-500 text-4xl sm:text-5xl mb-4"></i>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white mb-2">No notifications found</h3>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">You don't have any notifications matching your current filters.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 overflow-x-auto">
                    {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Professional Announcement Modal -->
    <div id="announcementModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 transition-opacity" onclick="closeAnnouncementModal()">
                <div class="absolute inset-0 bg-black opacity-60"></div>
            </div>

            <div class="relative bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl transform transition-all w-full max-w-4xl mx-auto" style="max-height: 90vh; display: flex; flex-direction: column;">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-[#055498] to-[#123a60] px-6 py-4 rounded-t-2xl flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-bullhorn text-white text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Announcement</h3>
                    </div>
                    <button onclick="closeAnnouncementModal()" class="w-9 h-9 rounded-full hover:bg-white/20 flex items-center justify-center text-white hover:bg-white/30 transition-all duration-200" aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="overflow-y-auto flex-1" style="max-height: calc(90vh - 80px);">
                    <div id="modalLoading" class="text-center py-16">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-[#055498] border-t-transparent mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">Loading announcement...</p>
                    </div>
                    <div id="modalContent" class="hidden">
                        <!-- Author Info -->
                        <div class="px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#055498] to-[#123a60] flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0" id="modalAuthorAvatar" style="font-size: 16px;">
                                    <!-- Initials or avatar -->
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-900 dark:text-white text-lg mb-1" id="modalAuthorName"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center space-x-2" id="modalDate">
                                        <i class="far fa-calendar-alt text-xs"></i>
                                        <span id="modalDateText"></span>
                                        <span class="mx-1">·</span>
                                        <i class="fas fa-globe-americas text-xs"></i>
                                        <span>Public</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="px-6 pt-6 pb-4">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white leading-tight mb-2" id="modalAnnouncementTitle" style="color: #055498;"></h2>
                        </div>

                        <!-- Banner Image -->
                        <div id="modalBanner" class="mb-6 hidden">
                            <div class="relative overflow-hidden rounded-lg mx-6 shadow-lg">
                                <img src="" alt="Banner" class="w-full h-auto" id="modalBannerImg" style="max-height: 500px; object-fit: cover; display: block;">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="px-6 pb-8">
                            <div class="text-gray-700 dark:text-gray-300 text-base leading-relaxed prose prose-lg max-w-none prose-headings:text-gray-900 prose-headings:dark:text-white prose-p:text-gray-700 prose-p:dark:text-gray-300 prose-strong:text-gray-900 prose-strong:dark:text-white prose-a:text-[#055498] prose-a:no-underline hover:prose-a:underline prose-ul:text-gray-700 prose-ul:dark:text-gray-300 prose-ol:text-gray-700 prose-ol:dark:text-gray-300 prose-li:text-gray-700 prose-li:dark:text-gray-300" id="modalDescription" style="line-height: 1.8;"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#0F172A] rounded-b-2xl flex-shrink-0">
                    <div class="flex items-center justify-end">
                        <button onclick="closeAnnouncementModal()" class="px-6 py-2.5 bg-gradient-to-r from-[#055498] to-[#123a60] text-white font-semibold rounded-lg hover:from-[#123a60] hover:to-[#055498] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Professional Announcement Modal Styles */
        #announcementModal {
            animation: fadeIn 0.3s ease-out;
        }
        
        #announcementModal > div > div {
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #announcementModal .prose {
            font-size: 1rem;
        }
        
        #announcementModal .prose p {
            margin-bottom: 1.25rem;
        }
        
        #announcementModal .prose ul,
        #announcementModal .prose ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        
        #announcementModal .prose li {
            margin-bottom: 0.5rem;
        }
        
        #announcementModal .prose h1,
        #announcementModal .prose h2,
        #announcementModal .prose h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        
        #announcementModal .prose img {
            border-radius: 0.5rem;
            margin: 1.5rem 0;
        }
        
        #announcementModal .prose a {
            color: #055498;
            font-weight: 500;
        }
        
        #announcementModal .prose a:hover {
            text-decoration: underline;
        }
        
        /* Mobile Responsive */
        @media (max-width: 640px) {
            #announcementModal > div > div {
                max-width: 95vw;
                margin: 1rem;
            }
            
            #announcementModal .prose {
                font-size: 0.9375rem;
            }
            
            #modalAnnouncementTitle {
                font-size: 1.5rem !important;
            }
        }
    </style>

    <script>
        // Set up axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        // Open announcement modal from notification
        window.openAnnouncementModalFromNotification = function(announcementUrl, notificationId) {
            // Extract announcement ID from URL
            const announcementIdMatch = announcementUrl.match(/\/announcements\/(\d+)/);
            if (!announcementIdMatch || !announcementIdMatch[1]) {
                // Fallback: navigate to URL
                window.location.href = announcementUrl;
                return;
            }
            
            const announcementId = announcementIdMatch[1];
            
            // Mark notification as read
            if (notificationId) {
                axios.post(`/notifications/${notificationId}/mark-as-read`)
                    .then(() => {
                        // Update UI
                        const $card = $(`.notification-item[data-notification-id="${notificationId}"]`);
                        if ($card.length) {
                            $card.removeClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                            $card.find('h3').removeClass('font-bold');
                            $card.find('.absolute.top-2.right-2').remove();
                            $card.find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                            
                            // Replace "Mark as read" button with "Mark as unread" button if exists
                            const $markReadBtn = $card.find('.notification-mark-read-menu').closest('button');
                            if ($markReadBtn.length) {
                                $markReadBtn.replaceWith(`
                                    <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                                        <i class="fas fa-check text-sm"></i>
                                        Mark as unread
                                    </button>
                                `);
                            }
                        }
                        
                        // Trigger event to update header popup
                        window.dispatchEvent(new CustomEvent('notificationMarkedAsRead', { 
                            detail: { notificationId: notificationId } 
                        }));
                    })
                    .catch(error => {
                        console.error('Failed to mark notification as read:', error);
                    });
            }
            
            // Open modal
            openAnnouncementModal(parseInt(announcementId));
        };
        
        // Open announcement modal
        window.openAnnouncementModal = function(announcementId) {
            const modal = document.getElementById('announcementModal');
            const modalLoading = document.getElementById('modalLoading');
            const modalContent = document.getElementById('modalContent');
            
            if (!modal) return;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Scroll to top of modal content
            const modalScrollContainer = modal.querySelector('.overflow-y-auto');
            if (modalScrollContainer) {
                modalScrollContainer.scrollTop = 0;
            }
            
            modalLoading.classList.remove('hidden');
            modalContent.classList.add('hidden');

            axios.get(`/announcements/api/${announcementId}/modal`)
                .then(response => {
                    const announcement = response.data.announcement;
                    
                    // Set author info
                    const authorName = announcement.author;
                    const authorInitials = authorName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                    document.getElementById('modalAuthorAvatar').textContent = authorInitials;
                    document.getElementById('modalAuthorName').textContent = authorName;
                    document.getElementById('modalDateText').textContent = announcement.created_at;

                    // Set banner
                    const bannerEl = document.getElementById('modalBanner');
                    if (announcement.banner_url) {
                        document.getElementById('modalBannerImg').src = announcement.banner_url;
                        bannerEl.classList.remove('hidden');
                    } else {
                        bannerEl.classList.add('hidden');
                    }

                    // Set title and description
                    document.getElementById('modalAnnouncementTitle').textContent = announcement.title;
                    // Render HTML description directly (from CKEditor, already sanitized)
                    const description = announcement.description || '';
                    document.getElementById('modalDescription').innerHTML = description;

                    modalLoading.classList.add('hidden');
                    modalContent.classList.remove('hidden');
                    
                    // Ensure scroll is at top after content loads
                    setTimeout(() => {
                        if (modalScrollContainer) {
                            modalScrollContainer.scrollTop = 0;
                        }
                    }, 100);
                })
                .catch(error => {
                    console.error('Error loading announcement:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load announcement details.',
                    });
                    closeAnnouncementModal();
                });
        };
        
        // Close announcement modal
        window.closeAnnouncementModal = function() {
            const modal = document.getElementById('announcementModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        };
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAnnouncementModal();
            }
        });

        // Configure SweetAlert Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Listen for notification deletion events from the header popup
        window.addEventListener('notificationDeleted', function(event) {
            const notificationId = event.detail.notificationId;
            const $card = $(`.notification-item[data-notification-id="${notificationId}"]`);
            if ($card.length) {
                $card.fadeOut(300, function() {
                    $(this).remove();
                    if ($('.notification-item').length === 0) {
                        location.reload();
                    }
                });
            }
        });
        
        // Listen for notification marked as read events from the header popup
        window.addEventListener('notificationMarkedAsRead', function(event) {
            const notificationId = event.detail.notificationId;
            const $card = $(`.notification-item[data-notification-id="${notificationId}"]`);
            if ($card.length) {
                $card.removeClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                $card.find('h3').removeClass('font-bold');
                $card.find('.absolute.top-2.right-2').remove();
                $card.find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                
                // Replace "Mark as read" button with "Mark as unread" button
                const $markReadBtn = $card.find('.notification-mark-read-menu').closest('button');
                if ($markReadBtn.length) {
                    $markReadBtn.replaceWith(`
                        <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                            <i class="fas fa-check text-sm"></i>
                            Mark as unread
                        </button>
                    `);
                }
            }
        });
        
        // Listen for notification marked as unread events from the header popup
        window.addEventListener('notificationMarkedAsUnread', function(event) {
            const notificationId = event.detail.notificationId;
            const $card = $(`.notification-item[data-notification-id="${notificationId}"]`);
            if ($card.length) {
                $card.addClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                $card.find('h3').addClass('font-bold');
                if ($card.find('.absolute.top-2.right-2.w-2.h-2.bg-blue-500').length === 0) {
                    $card.append('<div class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full"></div>');
                }
                $card.find('span.bg-gray-100.dark\\:bg-gray-700').replaceWith('<span class="px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(206, 32, 40, 0.1); color: #CE2028;">Unread</span>');
                
                // Replace "Mark as unread" button with "Mark as read" button
                const $markUnreadBtn = $card.find('.notification-mark-unread-menu').closest('button');
                if ($markUnreadBtn.length) {
                    $markUnreadBtn.replaceWith(`
                        <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                            <i class="fas fa-check text-sm"></i>
                            Mark as read
                        </button>
                    `);
                }
            }
        });
        
        $(document).ready(function() {
            // Three-dot menu button click handler
            $(document).on('click', '.notification-menu-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const notificationId = $(this).data('notification-id');
                const $menu = $(`.notification-menu-dropdown[data-menu-id="${notificationId}"]`);
                
                // Close all other menus
                $('.notification-menu-dropdown').not($menu).addClass('hidden');
                
                // Toggle current menu
                $menu.toggleClass('hidden');
            });
            
            // Mark as read from menu
            $(document).on('click', '.notification-mark-read-menu', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const notificationId = $(this).data('notification-id');
                const $menu = $(`.notification-menu-dropdown[data-menu-id="${notificationId}"]`);
                const $card = $(this).closest('.notification-item');
                $menu.addClass('hidden');
                
                axios.post(`/notifications/${notificationId}/mark-as-read`)
                    .then(response => {
                        if (response.data.success) {
                            // Update UI
                            $card.removeClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                            $card.find('h3').removeClass('font-bold');
                            $card.find('.absolute.top-2.right-2').remove();
                            $card.find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                            
                            // Replace "Mark as read" button with "Mark as unread" button
                            const $markReadBtn = $card.find('.notification-mark-read-menu').closest('button');
                            if ($markReadBtn.length) {
                                $markReadBtn.replaceWith(`
                                    <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                                        <i class="fas fa-check text-sm"></i>
                                        Mark as unread
                                    </button>
                                `);
                            }
                            
                            // Trigger event to update header popup
                            window.dispatchEvent(new CustomEvent('notificationMarkedAsRead', { 
                                detail: { notificationId: notificationId } 
                            }));
                            
                            Toast.fire({
                                icon: 'success',
                                title: 'Notification marked as read'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to mark notification as read.',
                        });
                    });
            });
            
            // Mark as unread from menu
            $(document).on('click', '.notification-mark-unread-menu', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const notificationId = $(this).data('notification-id');
                const $menu = $(`.notification-menu-dropdown[data-menu-id="${notificationId}"]`);
                const $card = $(this).closest('.notification-item');
                $menu.addClass('hidden');
                
                axios.post(`/notifications/${notificationId}/mark-as-unread`)
                    .then(response => {
                        if (response.data.success) {
                            // Update UI
                            $card.addClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                            $card.find('h3').addClass('font-bold');
                            // Add unread indicator if not exists
                            if ($card.find('.absolute.top-2.right-2.w-2.h-2.bg-blue-500').length === 0) {
                                $card.append('<div class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full"></div>');
                            }
                            $card.find('span.bg-gray-100.dark\\:bg-gray-700').replaceWith('<span class="px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(206, 32, 40, 0.1); color: #CE2028;">Unread</span>');
                            
                            // Replace "Mark as unread" button with "Mark as read" button
                            const $markUnreadBtn = $card.find('.notification-mark-unread-menu').closest('button');
                            if ($markUnreadBtn.length) {
                                $markUnreadBtn.replaceWith(`
                                    <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                                        <i class="fas fa-check text-sm"></i>
                                        Mark as read
                                    </button>
                                `);
                            }
                            
                            // Trigger event to update header popup
                            window.dispatchEvent(new CustomEvent('notificationMarkedAsUnread', { 
                                detail: { notificationId: notificationId } 
                            }));
                            
                            Toast.fire({
                                icon: 'success',
                                title: 'Notification marked as unread'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to mark notification as unread.',
                        });
                    });
            });
            
            // Delete notification from menu
            $(document).on('click', '.notification-delete-menu', function(e) {
                    e.preventDefault();
                e.stopPropagation();
                
                const notificationId = $(this).data('notification-id');
                const $menu = $(`.notification-menu-dropdown[data-menu-id="${notificationId}"]`);
                const $card = $(this).closest('.notification-item');
                $menu.addClass('hidden');
                
                Swal.fire({
                    title: 'Delete notification?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/notifications/${notificationId}`)
                            .then(response => {
                                if (response.data.success) {
                                    $card.fadeOut(300, function() {
                                        $(this).remove();
                                        // Check if no notifications left
                                        if ($('.notification-item').length === 0) {
                                            location.reload();
                }
                                    });
                                    
                                    // Trigger event to update header popup
                                    window.dispatchEvent(new CustomEvent('notificationDeleted', { 
                                        detail: { notificationId: notificationId } 
                                    }));
                                    
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Notification deleted'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete notification.',
                                });
                            });
                    }
                });
            });
            
            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.notification-menu-container').length) {
                    $('.notification-menu-dropdown').addClass('hidden');
                }
            });
            
            // Mark individual notification as read (legacy button - keeping for compatibility)
            $(document).on('click', '.mark-read-btn', function() {
                const notificationId = $(this).data('notification-id');
                const $btn = $(this);
                const $card = $btn.closest('.notification-item');

                axios.post(`/notifications/${notificationId}/mark-as-read`)
                    .then(response => {
                        if (response.data.success) {
                            // Update UI
                            $card.removeClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                            $card.find('h3').removeClass('font-bold');
                            $btn.closest('.flex').find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                            $btn.remove();
                            
                            Toast.fire({
                                icon: 'success',
                                title: 'Notification marked as read'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to mark notification as read.',
                        });
                    });
            });

            // Mark notification as read when clicking view link
            $(document).on('click', '.notification-link', function() {
                const notificationId = $(this).data('notification-id');
                if (notificationId) {
                    // Mark as read in background
                    axios.post(`/notifications/${notificationId}/mark-as-read`)
                        .then(response => {
                            if (response.data.success) {
                                // Update UI if still on page
                                const $card = $(this).closest('.notification-item');
                                if ($card.length) {
                                    $card.removeClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                                    $card.find('h3').removeClass('font-bold');
                                    $card.find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                                    $card.find('.mark-read-btn').remove();
                                }
                            }
                        })
                        .catch(error => {
                            // Silently fail - don't interrupt navigation
                            console.error('Failed to mark notification as read:', error);
                        });
                }
            });

            // Mark all as read
            $('#markAllReadBtn').on('click', function() {
                Swal.fire({
                    title: 'Mark all as read?',
                    text: 'This will mark all unread notifications as read.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#055498',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, mark all as read',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/notifications/mark-all-read')
                            .then(response => {
                                if (response.data.success) {
                                    // Update all unread notifications to read status in real-time
                                    $('.notification-item').each(function() {
                                        const $item = $(this);
                                        
                                        // Find unread badge by multiple methods
                                        let $unreadBadge = $item.find('span[style*="background-color: rgba(206, 32, 40"]');
                                        
                                        // Also try finding by text content
                                        if ($unreadBadge.length === 0) {
                                            $item.find('span').each(function() {
                                                const $span = $(this);
                                                const text = $span.text().trim();
                                                const style = $span.attr('style') || '';
                                                if (text === 'Unread' || style.includes('rgba(206, 32, 40')) {
                                                    $unreadBadge = $span;
                                                    return false; // break
                                                }
                                            });
                                        }
                                        
                                        // Check for any unread indicators
                                        const hasUnreadBadge = $unreadBadge.length > 0;
                                        const hasUnreadIndicator = $item.find('.absolute.top-2.right-2.w-2.h-2.bg-blue-500, .absolute.top-2.right-2').filter(function() {
                                            return $(this).hasClass('bg-blue-500') || $(this).css('background-color') === 'rgb(59, 130, 246)';
                                        }).length > 0;
                                        const hasUnreadStyling = $item.hasClass('bg-[#055498]/5') || $item.hasClass('dark:bg-[#055498]/10') || 
                                                               $item.css('background-color').includes('rgba(5, 84, 152');
                                        const hasBoldTitle = $item.find('h3').hasClass('font-bold');
                                        const hasMarkReadButton = $item.find('.notification-mark-read-menu').length > 0;
                                        
                                        // Update if any unread indicator is found
                                        if (hasUnreadBadge || hasUnreadIndicator || hasUnreadStyling || hasBoldTitle || hasMarkReadButton) {
                                            // Remove unread styling classes
                                            $item.removeClass('bg-[#055498]/5 dark:bg-[#055498]/10');
                                            $item.find('h3').removeClass('font-bold');
                                            
                                            // Remove unread indicator dot (try multiple selectors)
                                            $item.find('.absolute.top-2.right-2').each(function() {
                                                const $dot = $(this);
                                                if ($dot.hasClass('bg-blue-500') || $dot.hasClass('w-2') || $dot.css('background-color') === 'rgb(59, 130, 246)') {
                                                    $dot.remove();
                                                }
                                            });
                                            
                                            // Update unread badge to read
                                            if ($unreadBadge.length > 0) {
                                                $unreadBadge.replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                                            } else {
                                                // If badge not found by selector, find by text and replace
                                                $item.find('span').each(function() {
                                                    const $span = $(this);
                                                    if ($span.text().trim() === 'Unread') {
                                                        $span.replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded">Read</span>');
                                                        return false;
                                                    }
                                                });
                                            }
                                            
                                            // Replace "Mark as read" button with "Mark as unread" button
                                            const $markReadBtn = $item.find('.notification-mark-read-menu').closest('button');
                                            if ($markReadBtn.length) {
                                                const notificationId = $markReadBtn.data('notification-id');
                                                $markReadBtn.replaceWith(`
                                                    <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 dark:hover:bg-gray-600 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                                                        <i class="fas fa-check text-sm"></i>
                                                        Mark as unread
                                                    </button>
                                                `);
                                            }
                                        }
                                    });
                                    
                                    // Hide the "Mark all as read" button
                                    $('#markAllReadBtn').fadeOut(300);
                                    
                                    // Trigger event to update header popup
                                    window.dispatchEvent(new CustomEvent('allNotificationsMarkedAsRead'));
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'All notifications have been marked as read.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to mark all notifications as read.',
                                });
                            });
                    }
                });
            });
        });
    </script>
    
    @include('components.footer')
</body>
</html>
