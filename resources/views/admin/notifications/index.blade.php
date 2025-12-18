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
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2 border-b sm:border-b-0 sm:border-r border-gray-200 pb-3 sm:pb-0 sm:pr-4">
                    <span class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-0">Status:</span>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.notifications.index', ['filter' => 'all', 'type' => $type]) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'all' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $filter === 'all' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            All
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'unread', 'type' => $type]) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'unread' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $filter === 'unread' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            Unread
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => 'read', 'type' => $type]) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $filter === 'read' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $filter === 'read' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            Read
                        </a>
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                    <span class="text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-0">Type:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'all']) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $type === 'all' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $type === 'all' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            All Types
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'pending_registration']) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $type === 'pending_registration' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $type === 'pending_registration' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            Pending Registrations
                        </a>
                        <a href="{{ route('admin.notifications.index', ['filter' => $filter, 'type' => 'announcement']) }}" 
                           class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded-lg transition {{ $type === 'announcement' ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}" 
                           style="{{ $type === 'announcement' ? 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);' : '' }}">
                            Announcements
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
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 p-4 sm:p-6 border-l-4 {{ $borderColor }} {{ !$notification->is_read ? 'bg-[#055498]/5' : '' }}">
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
                                    <a href="{{ $notification->url }}" class="px-2 py-1 text-xs font-medium text-white rounded hover:opacity-90 transition whitespace-nowrap" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                        <i class="fas fa-external-link-alt mr-1"></i>View
                                    </a>
                                @endif
                                @if(!$notification->is_read)
                                    <button class="mark-read-btn px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition whitespace-nowrap" data-notification-id="{{ $notification->id }}">
                                        <i class="fas fa-check mr-1"></i>Mark as read
                                    </button>
                                @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    $(document).ready(function() {
        // Mark individual notification as read
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

