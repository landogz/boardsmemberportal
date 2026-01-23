@extends('admin.layout')

@section('title', 'Board Regulations')

@php
    $pageTitle = 'Board Regulations';
    $headerActions = [];
    if (Auth::user()->hasPermission('create board regulations')) {
        $headerActions[] = [
            'url' => route('admin.board-regulations.create'),
            'text' => 'Add New Board Regulation',
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
    #regulationsTable td {
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
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manage Board Regulations</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">View and manage all board regulations</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-3 sm:p-4 lg:p-6">
        <div class="overflow-x-auto">
            <table id="regulationsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PDF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($regulations as $regulation)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $regulation->title }}</div>
                            @if($regulation->description)
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($regulation->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $regulation->version ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $regulation->effective_date ? $regulation->effective_date->format('M d, Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $regulation->approved_date ? $regulation->approved_date->format('M d, Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($regulation->uploader)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $profileMedia = $regulation->uploader->profile_picture ? \App\Models\MediaLibrary::find($regulation->uploader->profile_picture) : null;
                                            $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($regulation->uploader->first_name . ' ' . $regulation->uploader->last_name) . '&size=40&background=055498&color=fff';
                                        @endphp
                                        <img src="{{ $profileUrl }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border-2" style="border-color: #055498;">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $regulation->uploader->first_name }} {{ $regulation->uploader->last_name }}
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">N/A</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($regulation->pdf)
                                <button 
                                    onclick="openGlobalPdfModal('{{ route('admin.board-regulations.pdf', $regulation->id) }}', '{{ addslashes($regulation->title) }}')"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 hover:bg-green-200 transition-colors cursor-pointer"
                                    title="Click to view PDF"
                                >
                                    <i class="fas fa-file-pdf mr-1"></i> View PDF
                                </button>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    No PDF
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="relative inline-block text-left" style="position: relative;">
                                <button type="button" class="action-dropdown-btn inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#055498]" data-regulation-id="{{ $regulation->id }}">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div class="action-dropdown-menu hidden w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" data-dropdown-id="{{ $regulation->id }}">
                                    <div class="py-1" role="menu">
                                        @can('edit board regulations')
                                        <a href="{{ route('admin.board-regulations.edit', $regulation->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 flex items-center" role="menuitem">
                                            <i class="fas fa-edit w-4 mr-3 text-blue-600"></i>
                                            Edit Regulation
                                        </a>
                                        @endcan
                                        @can('view board regulations')
                                        <a href="{{ route('admin.board-regulations.history', $regulation->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900 flex items-center" role="menuitem">
                                            <i class="fas fa-history w-4 mr-3 text-purple-600"></i>
                                            View History
                                        </a>
                                        @endcan
                                        @can('delete board regulations')
                                        <button type="button" class="delete-regulation-btn w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center" role="menuitem" data-regulation-id="{{ $regulation->id }}">
                                            <i class="fas fa-trash w-4 mr-3"></i>
                                            Delete Regulation
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($regulations->isEmpty())
            <div class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No board regulations found</p>
                    <p class="text-sm mt-2">Get started by creating your first board regulation</p>
                    <a href="{{ route('admin.board-regulations.create') }}" class="mt-4 inline-block px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-plus mr-2"></i>Add New Board Regulation
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Using global PDF modal from components/pdf-modal.blade.php -->
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        @if($regulations->isNotEmpty())
        $('#regulationsTable').DataTable({
            order: [[2, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                search: "Search regulations:",
                lengthMenu: "Show _MENU_ regulations per page",
                info: "Showing _START_ to _END_ of _TOTAL_ regulations",
                infoEmpty: "No regulations found",
                infoFiltered: "(filtered from _MAX_ total regulations)"
            }
        });
        @endif
    });

    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Dropdown menu toggle
    $(document).on('click', '.action-dropdown-btn', function(e) {
        e.stopPropagation();
        const regulationId = $(this).data('regulation-id');
        const $dropdown = $(`.action-dropdown-menu[data-dropdown-id="${regulationId}"]`);
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

    function deleteRegulation(id) {
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
                axios.delete(`/admin/board-regulations/${id}`)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.data.message,
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

    // Using global PDF modal functions from components/pdf-modal.blade.php
    
    // Delete regulation from dropdown
    $(document).on('click', '.delete-regulation-btn', function() {
        const id = $(this).data('regulation-id');
        deleteRegulation(id);
    });
</script>
@endpush

