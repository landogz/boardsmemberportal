@extends('admin.layout')

@section('title', 'Notifications')

@php
    $pageTitle = 'Notifications';
@endphp

@section('content')
    <div class="space-y-4 sm:space-y-6 p-4 sm:p-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Stay updated with all board activities</p>
                </div>
                <button id="markAllReadBtn" class="w-full sm:w-auto px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-check-double mr-2"></i>Mark all as read
                </button>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3 sm:gap-4">
                <!-- Status Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                    <span class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-0">Status:</span>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'all' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $filter === 'all' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            All
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'unread' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $filter === 'unread' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            Unread
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'read']) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'read' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
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
                        'pending_registration' => ['icon' => 'fa-clock', 'color' => 'brand'],
                        'announcement' => ['icon' => 'fa-bullhorn', 'color' => 'brand'],
                        'notice' => ['icon' => 'fa-file-text', 'color' => 'brand'],
                        'default' => ['icon' => 'fa-bell', 'color' => 'brand']
                    ];
                    $notificationType = $iconMap[$notification->type] ?? $iconMap['default'];
                    // Use brand colors
                    $borderColor = 'border-[#055498]';
                    $bgColor = 'bg-[#055498]/10';
                    $iconColor = 'text-[#055498]';
                @endphp
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 p-4 sm:p-6 border-l-4 {{ $borderColor }} {{ !$notification->is_read ? 'bg-[#055498]/5' : '' }} relative group">
                    <div class="flex items-start space-x-3 sm:space-x-4">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 {{ $bgColor }} rounded-full flex items-center justify-center">
                            <i class="fas {{ $notificationType['icon'] }} {{ $iconColor }} text-sm sm:text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-1 sm:gap-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800 {{ !$notification->is_read ? 'font-bold' : '' }}">
                                    {{ $notification->title }}
                                </h3>
                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-gray-600 mb-3 break-words">{{ $notification->message }}</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-1 text-xs font-medium {{ $bgColor }} text-[#055498] rounded">
                                    {{ ucwords(str_replace('_', ' ', $notification->type)) }}
                                </span>
                                @if(!$notification->is_read)
                                    <span class="px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(206, 32, 40, 0.1); color: #CE2028;">Unread</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Read</span>
                                @endif
                                @if($notification->url)
                                    @if($notification->type === 'announcement' && str_contains($notification->url, '/announcements/'))
                                        <button onclick="openAnnouncementModalFromNotification('{{ $notification->url }}', {{ $notification->id }})" class="px-2 py-1 text-xs font-medium text-white rounded hover:opacity-90 transition whitespace-nowrap" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </button>
                                    @else
                                        <a href="{{ $notification->url }}" class="px-2 py-1 text-xs font-medium text-white rounded hover:opacity-90 transition whitespace-nowrap" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
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
                            <button type="button" class="notification-menu-btn w-8 h-8 rounded-full hover:bg-gray-200 hover:bg-gray-600 flex items-center justify-center transition-opacity opacity-0 group-hover:opacity-100" data-notification-id="{{ $notification->id }}">
                                <i class="fas fa-ellipsis-h text-gray-600 text-sm"></i>
                            </button>
                            <div class="notification-menu-dropdown absolute right-0 top-8 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-50 hidden min-w-[180px]" data-menu-id="{{ $notification->id }}">
                                <div class="py-1">
                                    @if(!$notification->is_read)
                                        <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-check text-sm"></i>
                                            Mark as read
                                        </button>
                                    @else
                                        <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-check text-sm"></i>
                                            Mark as unread
                                        </button>
                                    @endif
                                    <button type="button" class="notification-delete-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="{{ $notification->id }}">
                                        <i class="fas fa-times text-sm"></i>
                                        Delete this notification
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                    <i class="fas fa-bell-slash text-gray-400 text-4xl sm:text-5xl mb-4"></i>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">No notifications found</h3>
                    <p class="text-sm sm:text-base text-gray-600">You don't have any notifications matching your current filters.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 overflow-x-auto">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
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
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
                    // Update UI - find the notification card
                    let $card = $(`.bg-white.rounded-lg.shadow-sm:has(.notification-menu-btn[data-notification-id="${notificationId}"])`);
                    if ($card.length === 0) {
                        // Try alternative selector
                        $card = $(`.bg-white.rounded-lg.shadow-sm:has(button[onclick*="${notificationId}"])`);
                    }
                    if ($card.length) {
                        $card.removeClass('bg-[#055498]/5');
                        $card.find('h3').removeClass('font-bold');
                        $card.find('.absolute.top-2.right-2').remove();
                        $card.find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Read</span>');
                        
                        // Replace "Mark as read" button with "Mark as unread" button if exists
                        const $markReadBtn = $card.find('.notification-mark-read-menu').closest('button');
                        if ($markReadBtn.length) {
                            $markReadBtn.replaceWith(`
                                <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 whitespace-nowrap transition" data-notification-id="${notificationId}">
                                    <i class="fas fa-check text-sm"></i>
                                    Mark as unread
                                </button>
                            `);
                        }
                    }
                })
                .catch(error => {
                    console.error('Failed to mark notification as read:', error);
                });
        }
        
        // Open modal
        if (typeof window.openAnnouncementModal === 'function') {
            window.openAnnouncementModal(parseInt(announcementId));
        } else {
            // Fallback: navigate to announcement page
            window.location.href = announcementUrl;
        }
    };

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
            const $card = $(this).closest('.bg-white');
            $menu.addClass('hidden');
            
            axios.post(`/notifications/${notificationId}/mark-as-read`)
                .then(response => {
                    if (response.data.success) {
                        // Update UI
                        $card.removeClass('bg-[#055498]/5');
                        $card.find('h3').removeClass('font-bold');
                        $card.find('.absolute.top-2.right-2').remove();
                        $card.find('span[style*="background-color: rgba(206, 32, 40"]').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Read</span>');
                        
                        // Replace "Mark as read" button with "Mark as unread" button
                        const $markReadBtn = $card.find('.notification-mark-read-menu').closest('button');
                        if ($markReadBtn.length) {
                            $markReadBtn.replaceWith(`
                                <button type="button" class="notification-mark-unread-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 transition" data-notification-id="${notificationId}">
                                    <i class="fas fa-check text-sm"></i>
                                    Mark as unread
                                </button>
                            `);
                        }
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Notification marked as read',
                            showConfirmButton: false,
                            timer: 2000
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
            const $card = $(this).closest('.bg-white');
            $menu.addClass('hidden');
            
            axios.post(`/notifications/${notificationId}/mark-as-unread`)
                .then(response => {
                    if (response.data.success) {
                        // Update UI
                        $card.addClass('bg-[#055498]/5');
                        $card.find('h3').addClass('font-bold');
                        // Add unread indicator if not exists
                        if ($card.find('.absolute.top-2.right-2.w-2.h-2.bg-blue-500').length === 0) {
                            $card.append('<div class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full"></div>');
                        }
                        $card.find('span.bg-gray-100.text-gray-800').replaceWith('<span class="px-2 py-1 text-xs font-medium rounded" style="background-color: rgba(206, 32, 40, 0.1); color: #CE2028;">Unread</span>');
                        
                        // Replace "Mark as unread" button with "Mark as read" button
                        const $markUnreadBtn = $card.find('.notification-mark-unread-menu').closest('button');
                        if ($markUnreadBtn.length) {
                            $markUnreadBtn.replaceWith(`
                                <button type="button" class="notification-mark-read-menu w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700 flex items-center gap-2 transition" data-notification-id="${notificationId}">
                                    <i class="fas fa-check text-sm"></i>
                                    Mark as read
                                </button>
                            `);
                        }
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Notification marked as unread',
                            showConfirmButton: false,
                            timer: 2000
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
            const $card = $(this).closest('.bg-white');
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
                                    if ($('.bg-white.rounded-lg.shadow-sm').length === 0) {
                                        location.reload();
                                    }
                                });
                                
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Notification deleted',
                                    showConfirmButton: false,
                                    timer: 2000
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
            const $card = $btn.closest('.bg-white');

            axios.post(`/notifications/${notificationId}/mark-as-read`)
                .then(response => {
                    if (response.data.success) {
                        // Update UI
                        $card.removeClass('bg-blue-50');
                        $card.find('h3').removeClass('font-bold');
                        $btn.closest('.flex').find('.bg-red-100').replaceWith('<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Read</span>');
                        $btn.remove();
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
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'All notifications have been marked as read.',
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
                                title: 'Error',
                                text: 'Failed to mark all notifications as read.',
                            });
                        });
                }
            });
        });
    });
</script>
@endpush

