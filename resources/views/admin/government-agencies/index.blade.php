@extends('admin.layout')

@section('title', 'Manage Agencies')

@php
    $pageTitle = 'Manage Agencies';
    $headerActions = [];
    if (Auth::user()->hasPermission('create government agencies')) {
        $headerActions[] = [
            'url' => route('admin.government-agencies.create'),
            'text' => 'Add New Agency',
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
    .status-inactive {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }
    .agency-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <!-- Page Title -->
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manage Government Agencies</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">View and manage all government agencies in the system</p>
    </div>

    <!-- Agencies Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <div></div>
                @can('delete government agencies')
                <button 
                    id="bulkDeleteBtn"
                    class="px-4 py-2 text-sm font-semibold text-white rounded-lg shadow-sm transition-all duration-200 disabled:opacity-40"
                    style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);"
                    disabled
                >
                    <i class="fas fa-trash mr-2"></i>Delete Selected
                </button>
                @endcan
            </div>
            <div class="overflow-x-auto">
                <table id="agenciesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllAgencies" class="h-4 w-4 text-[#055498] border-gray-300 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($agencies as $agency)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $agency->id }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <input 
                                type="checkbox" 
                                class="agency-select h-4 w-4 text-[#055498] border-gray-300 rounded"
                                value="{{ $agency->id }}"
                                {{ $agency->users()->count() > 0 ? 'disabled' : '' }}
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($agency->logo)
                                <img src="{{ asset('storage/' . $agency->logo->file_path) }}" alt="{{ $agency->name }}" class="agency-logo">
                            @else
                                <div class="agency-logo bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $agency->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $agency->code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($agency->description ?? 'No description', 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $agency->users()->count() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge {{ $agency->is_active ? 'status-active' : 'status-inactive' }}" id="status-{{ $agency->id }}">
                                {{ $agency->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @can('edit government agencies')
                                <button 
                                    onclick="toggleStatus({{ $agency->id }})" 
                                    class="px-3 py-1 text-xs rounded-lg transition-colors {{ $agency->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}"
                                    title="{{ $agency->is_active ? 'Deactivate' : 'Activate' }}"
                                >
                                    <i class="fas {{ $agency->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                                <a 
                                    href="{{ route('admin.government-agencies.edit', $agency->id) }}" 
                                    class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete government agencies')
                                <button 
                                    onclick="deleteAgency({{ $agency->id }})" 
                                    class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors"
                                    title="Delete"
                                    {{ $agency->users()->count() > 0 ? 'disabled' : '' }}
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-building text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No agencies found</p>
                                <p class="text-sm mt-2">Get started by creating your first government agency</p>
                                @can('create government agencies')
                                <a href="{{ route('admin.government-agencies.create') }}" class="mt-4 inline-block px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                    <i class="fas fa-plus mr-2"></i>Add New Agency
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize DataTable
    $(document).ready(function() {
        const table = $('#agenciesTable').DataTable({
            order: [[2, 'asc']], // Sort by agency name (second visible column after checkbox)
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                search: "Search agencies:",
                lengthMenu: "Show _MENU_ agencies per page",
                info: "Showing _START_ to _END_ of _TOTAL_ agencies",
                infoEmpty: "No agencies found",
                infoFiltered: "(filtered from _MAX_ total agencies)"
            }
        });
        // Handle row checkbox change
        function updateBulkDeleteState() {
            const anyChecked = $('.agency-select:checked').length > 0;
            $('#bulkDeleteBtn').prop('disabled', !anyChecked);
        }

        $(document).on('change', '.agency-select', function() {
            updateBulkDeleteState();

            const total = $('.agency-select:not(:disabled)').length;
            const checked = $('.agency-select:checked').length;
            $('#selectAllAgencies').prop('checked', total > 0 && total === checked);
        });

        // Select all toggle (only agencies without users are selectable)
        $('#selectAllAgencies').on('change', function() {
            const checked = $(this).is(':checked');
            $('.agency-select:not(:disabled)').prop('checked', checked);
            updateBulkDeleteState();
        });

        // Bulk delete click
        $('#bulkDeleteBtn').on('click', function() {
            const ids = $('.agency-select:checked').map(function() { return $(this).val(); }).get();

            if (ids.length === 0) {
                return;
            }

            Swal.fire({
                title: 'Delete selected agencies?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                axios.delete('{{ route("admin.government-agencies.bulk-delete") }}', {
                    data: { ids }
                }).then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: response.data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'An error occurred while deleting agencies.'
                        });
                    }
                }).catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred while deleting agencies.'
                    });
                });
            });
        });
    });

    // Set up axios defaults
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Toggle agency status
    function toggleStatus(id) {
        axios.post(`/admin/government-agencies/${id}/toggle-status`)
            .then(response => {
                if (response.data.success) {
                    const statusBadge = document.getElementById(`status-${id}`);
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    const toggleBtn = row.querySelector('button[onclick*="toggleStatus"]');
                    
                    if (response.data.is_active) {
                        statusBadge.className = 'status-badge status-active';
                        statusBadge.textContent = 'Active';
                        toggleBtn.className = 'px-3 py-1 text-xs rounded-lg transition-colors bg-yellow-100 text-yellow-800 hover:bg-yellow-200';
                        toggleBtn.innerHTML = '<i class="fas fa-toggle-on"></i>';
                    } else {
                        statusBadge.className = 'status-badge status-inactive';
                        statusBadge.textContent = 'Inactive';
                        toggleBtn.className = 'px-3 py-1 text-xs rounded-lg transition-colors bg-green-100 text-green-800 hover:bg-green-200';
                        toggleBtn.innerHTML = '<i class="fas fa-toggle-off"></i>';
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
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

    // Delete agency
    function deleteAgency(id) {
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
                axios.delete(`/admin/government-agencies/${id}`)
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
                            text: error.response?.data?.message || 'Cannot delete agency. There are users associated with this agency.'
                        });
                    });
            }
        });
    }
</script>
@endpush
