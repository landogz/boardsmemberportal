@extends('admin.layout')

@section('title', 'Referendums')

@php
    $pageTitle = 'Referendums';
    $headerActions = [];
    if (Auth::user()->hasPermission('create referendum')) {
        $headerActions[] = [
            'url' => route('admin.referendums.create'),
            'text' => 'Create Referendum',
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
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    .status-expired {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
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
    #referendumsTable td {
        position: relative;
        overflow: visible;
    }
    
    /* Ensure dropdown menu doesn't cause scrollbar */
    .action-dropdown-menu {
        position: fixed !important;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-800">All Referendums</h2>
            <p class="text-sm text-gray-600 mt-1">Manage referendum posts, voting, and comments</p>
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

        <div class="overflow-x-auto">
            <table id="referendumsTable" class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Created By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Expires At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Votes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Comments</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($referendums as $referendum)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $referendum->title }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($referendum->content, 100) }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $referendum->creator->first_name }} {{ $referendum->creator->last_name }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="status-badge {{ $referendum->status === 'active' ? 'status-active' : 'status-expired' }}">
                                {{ ucfirst($referendum->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $referendum->expires_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <div class="flex items-center space-x-2">
                                <span class="text-green-600">✓ {{ $referendum->accept_count }}</span>
                                <span class="text-red-600">✗ {{ $referendum->decline_count }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $referendum->total_comments }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="relative inline-block text-left" style="position: relative;">
                                <button type="button" class="action-dropdown-btn inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#055498]" data-referendum-id="{{ $referendum->id }}">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div class="action-dropdown-menu hidden w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" data-dropdown-id="{{ $referendum->id }}">
                                    <div class="py-1" role="menu">
                                        <a href="{{ route('admin.referendums.show', $referendum->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 flex items-center" role="menuitem">
                                            <i class="fas fa-eye w-4 mr-3 text-blue-600"></i>
                                            View Details
                                        </a>
                                        @can('edit referendum')
                                        <a href="{{ route('admin.referendums.edit', $referendum->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-900 flex items-center" role="menuitem">
                                            <i class="fas fa-edit w-4 mr-3 text-green-600"></i>
                                            Edit Referendum
                                        </a>
                                        @endcan
                                        @can('delete referendum')
                                        <button type="button" class="delete-referendum-btn w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center" role="menuitem" data-referendum-id="{{ $referendum->id }}">
                                            <i class="fas fa-trash w-4 mr-3"></i>
                                            Delete Referendum
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No referendums found. <a href="{{ route('admin.referendums.create') }}" class="text-blue-600 hover:underline">Create one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize DataTable
    $(document).ready(function() {
        @if($referendums->count() > 0)
        $('#referendumsTable').DataTable({
            order: [[3, 'desc']], // Sort by expires_at
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [
                { targets: -1, orderable: false } // Actions column is not sortable
            ],
            language: {
                search: "Search referendums:",
                lengthMenu: "Show _MENU_ referendums per page",
                info: "Showing _START_ to _END_ of _TOTAL_ referendums",
                infoEmpty: "No referendums found",
                infoFiltered: "(filtered from _MAX_ total referendums)"
            }
        });
        @endif
    });

    // Set up axios defaults
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Dropdown menu toggle
    $(document).on('click', '.action-dropdown-btn', function(e) {
        e.stopPropagation();
        const referendumId = $(this).data('referendum-id');
        const $dropdown = $(`.action-dropdown-menu[data-dropdown-id="${referendumId}"]`);
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

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.action-dropdown-btn, .action-dropdown-menu').length) {
            $('.action-dropdown-menu').addClass('hidden');
        }
    });

    // Close dropdown when clicking on menu item
    $(document).on('click', '.action-dropdown-menu a, .action-dropdown-menu button', function() {
        $(this).closest('.action-dropdown-menu').addClass('hidden');
    });

    // Delete referendum
    function deleteReferendum(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/admin/referendums/${id}`)
                    .then(response => {
                        if (response.data.success || response.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.data?.message || 'Referendum has been deleted.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.response?.data?.message || 'An error occurred. Please try again.'
                        });
                    });
            }
        });
    }

    // Handle delete from dropdown
    $(document).on('click', '.delete-referendum-btn', function() {
        const referendumId = $(this).data('referendum-id');
        deleteReferendum(referendumId);
    });
</script>
@endpush

