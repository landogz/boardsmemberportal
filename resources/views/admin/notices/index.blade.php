@extends('admin.layout')

@section('title', 'Notices')

@php
    $pageTitle = 'Notices';
    $headerActions = [];
    if (Auth::user()->hasPermission('create notices')) {
        $headerActions[] = [
            'url' => route('admin.notices.create'),
            'text' => 'Create Notice',
            'icon' => 'fas fa-plus',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ];
    }
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .notice-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .type-meeting {
        background-color: rgba(5, 84, 152, 0.1);
        color: #055498;
    }
    .type-agenda {
        background-color: rgba(206, 32, 40, 0.1);
        color: #CE2028;
    }
    .type-board-issuances {
        background-color: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
    }
    .type-other {
        background-color: rgba(156, 163, 175, 0.1);
        color: #6B7280;
    }
    .action-dropdown-menu {
        animation: fadeIn 0.15s ease-out;
        min-width: 180px;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .action-dropdown-btn {
        transition: all 0.2s ease;
    }
    
    .action-dropdown-btn:hover {
        background-color: #f3f4f6;
    }
    
    /* Prevent table overflow from dropdown */
    .dataTables_wrapper {
        overflow-x: auto !important;
        overflow-y: visible !important;
    }
    
    .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: visible !important;
    }
    
    /* Fix dropdown positioning in table cells */
    #noticesTable td {
        position: relative;
        overflow: visible;
    }
    
    /* Ensure dropdown menu doesn't cause scrollbar */
    .action-dropdown-menu {
        position: fixed !important;
    }
    
    /* Overlapping avatars */
    .avatar-stack {
        display: flex;
        align-items: center;
    }
    
    .avatar-stack .avatar-item {
        position: relative;
        margin-left: -12px;
        transition: transform 0.2s ease, z-index 0.2s ease;
    }
    
    .avatar-stack .avatar-item:first-child {
        margin-left: 0;
    }
    
    .avatar-stack .avatar-item:hover {
        transform: translateY(-4px) scale(1.1);
        z-index: 10;
    }
    
    .avatar-stack .avatar-item img {
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Tooltip */
    .avatar-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background-color: #1f2937;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        z-index: 20;
        margin-bottom: 8px;
    }
    
    .avatar-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #1f2937;
    }
    
    .avatar-item:hover .avatar-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-800">All Notices</h2>
            <p class="text-sm text-gray-600 mt-1">Manage notices and their visibility</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if($notices->count() > 0)
        <div class="overflow-x-auto">
            <table id="noticesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowed Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notices as $notice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeClass = 'type-other';
                                if ($notice->notice_type === 'Notice of Meeting') {
                                    $typeClass = 'type-meeting';
                                } elseif ($notice->notice_type === 'Agenda') {
                                    $typeClass = 'type-agenda';
                                } elseif ($notice->notice_type === 'Board Issuances') {
                                    $typeClass = 'type-board-issuances';
                                }
                            @endphp
                            <span class="notice-type-badge {{ $typeClass }}">
                                {{ $notice->notice_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($notice->title, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 capitalize">{{ $notice->meeting_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @php
                                        $profileMedia = $notice->creator->profile_picture ? \App\Models\MediaLibrary::find($notice->creator->profile_picture) : null;
                                        $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($notice->creator->first_name . ' ' . $notice->creator->last_name) . '&size=40&background=055498&color=fff';
                                    @endphp
                                    <img src="{{ $profileUrl }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border-2" style="border-color: #055498;">
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $notice->creator->first_name }} {{ $notice->creator->last_name }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="avatar-stack">
                                @php
                                    $maxVisible = 7;
                                    $users = $notice->allowedUsers;
                                    $visibleUsers = $users->take($maxVisible);
                                    $remainingCount = $users->count() - $maxVisible;
                                @endphp
                                @foreach($visibleUsers as $user)
                                    @php
                                        $profileMedia = $user->profile_picture ? \App\Models\MediaLibrary::find($user->profile_picture) : null;
                                        $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=32&background=055498&color=fff';
                                    @endphp
                                    <div class="avatar-item relative">
                                        <img src="{{ $profileUrl }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-8 h-8 rounded-full object-cover cursor-pointer">
                                        <div class="avatar-tooltip">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </div>
                                    </div>
                                @endforeach
                                @if($remainingCount > 0)
                                    <div class="avatar-item relative">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-semibold text-gray-600 cursor-pointer shadow-sm">
                                            +{{ $remainingCount }}
                                        </div>
                                        <div class="avatar-tooltip">
                                            {{ $remainingCount }} more user(s)
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-order="{{ $notice->created_at->format('Y-m-d H:i:s') }}">
                            {{ $notice->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="relative inline-block text-left" style="position: relative;">
                                <button type="button" class="action-dropdown-btn inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#055498]" data-notice-id="{{ $notice->id }}">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div class="action-dropdown-menu hidden w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" data-dropdown-id="{{ $notice->id }}">
                                    <div class="py-1" role="menu">
                                        <a href="{{ route('admin.notices.show', $notice->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 flex items-center" role="menuitem">
                                            <i class="fas fa-eye w-4 mr-3 text-blue-600"></i>
                                            View Details
                                        </a>
                                        @if(Auth::user()->hasPermission('edit notices'))
                                        <a href="{{ route('admin.notices.edit', $notice->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-900 flex items-center" role="menuitem">
                                            <i class="fas fa-edit w-4 mr-3 text-green-600"></i>
                                            Edit Notice
                                        </a>
                                        @endif
                                        @if(Auth::user()->hasPermission('delete notices'))
                                        <button type="button" class="delete-notice-btn w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center" role="menuitem" data-notice-id="{{ $notice->id }}">
                                            <i class="fas fa-trash w-4 mr-3"></i>
                                            Delete Notice
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No notices found</p>
            @if(Auth::user()->hasPermission('create notices'))
            <a href="{{ route('admin.notices.create') }}" class="mt-4 inline-block px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors">
                Create Your First Notice
            </a>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#noticesTable').DataTable({
            order: [[5, 'desc']], // Sort by created date descending
            pageLength: 15,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ notices per page",
                info: "Showing _START_ to _END_ of _TOTAL_ notices",
                infoEmpty: "No notices found",
                infoFiltered: "(filtered from _MAX_ total notices)",
            }
        });
    });

    // Dropdown menu toggle
    $(document).on('click', '.action-dropdown-btn', function(e) {
        e.stopPropagation();
        const noticeId = $(this).data('notice-id');
        const $dropdown = $(`.action-dropdown-menu[data-dropdown-id="${noticeId}"]`);
        const $button = $(this);
        
        // Close all other dropdowns
        $('.action-dropdown-menu').not($dropdown).addClass('hidden');
        
        // Toggle current dropdown
        if ($dropdown.hasClass('hidden')) {
            // Calculate position relative to viewport
            const buttonOffset = $button.offset();
            const buttonWidth = $button.outerWidth();
            const buttonHeight = $button.outerHeight();
            const dropdownWidth = 192; // w-48 = 192px
            const windowWidth = $(window).width();
            const windowHeight = $(window).height();
            
            // Calculate right position (align to right edge of button)
            let rightPosition = windowWidth - buttonOffset.left - buttonWidth;
            
            // If dropdown would go off screen, align to left edge instead
            if (rightPosition + dropdownWidth > windowWidth) {
                rightPosition = windowWidth - buttonOffset.left - dropdownWidth;
            }
            
            // Calculate top position (below button)
            let topPosition = buttonOffset.top + buttonHeight + 8;
            
            // If dropdown would go off bottom of screen, show above button
            if (topPosition + 200 > windowHeight) {
                topPosition = buttonOffset.top - 200 - 8;
            }
            
            // Position dropdown using fixed positioning
            $dropdown.css({
                'position': 'fixed',
                'top': topPosition + 'px',
                'right': rightPosition + 'px',
                'left': 'auto',
                'z-index': 1050
            });
        }
        
        $dropdown.toggleClass('hidden');
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.action-dropdown-btn, .action-dropdown-menu').length) {
            $('.action-dropdown-menu').addClass('hidden');
        }
    });

    // Close dropdown when clicking on a menu item
    $(document).on('click', '.action-dropdown-menu button, .action-dropdown-menu a', function() {
        $(this).closest('.action-dropdown-menu').addClass('hidden');
    });

    function deleteNotice(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/notices/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete notice.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Failed to delete notice.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // Handle delete from dropdown
    $(document).on('click', '.delete-notice-btn', function() {
        const noticeId = $(this).data('notice-id');
        deleteNotice(noticeId);
    });
</script>
@endpush
@endsection

