@extends('admin.layout')

@section('title', 'Pending Registrations')

@php
    $pageTitle = 'Pending Registrations';
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    /* Dropdown Menu Styles */
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
    #pendingRegistrationsTable td {
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
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Pending Registrations</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Review and manage newly registered user accounts</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            @if($pendingRegistrations->count() > 0)
            <div class="overflow-x-auto">
                <table id="pendingRegistrationsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Government Agency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Representative Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Registered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingRegistrations as $registration)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $registration->pre_nominal_title }} {{ $registration->first_name }} {{ $registration->middle_initial ? $registration->middle_initial . '.' : '' }} {{ $registration->last_name }} {{ $registration->post_nominal_title ? ', ' . $registration->post_nominal_title : '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $registration->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $registration->username }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($registration->governmentAgency)
                                        {{ $registration->governmentAgency->name }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($registration->representative_type)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $registration->representative_type === 'Board Member' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $registration->representative_type }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $registration->created_at->format('M d, Y h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @can('view pending registrations')
                                    <a href="{{ route('admin.pending-registrations.show', $registration->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white rounded-lg hover:opacity-90 transition" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                                        <i class="fas fa-eye mr-2"></i>
                                        View
                                    </a>
                                    @endcan
                                    @can('approve pending registrations')
                                    <button type="button" class="approve-btn inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition" data-registration-id="{{ $registration->id }}" data-registration-name="{{ $registration->pre_nominal_title }} {{ $registration->first_name }} {{ $registration->last_name }}">
                                        <i class="fas fa-check mr-2"></i>
                                        Approve
                                    </button>
                                    @endcan
                                    @can('disapprove pending registrations')
                                    <button type="button" class="disapprove-btn inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition" data-registration-id="{{ $registration->id }}" data-registration-name="{{ $registration->pre_nominal_title }} {{ $registration->first_name }} {{ $registration->last_name }}">
                                        <i class="fas fa-times mr-2"></i>
                                        Disapprove
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No pending registrations</p>
                <p class="text-gray-400 text-sm mt-2">All registrations have been reviewed.</p>
            </div>
            @endif
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
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    $(document).ready(function() {
        @if($pendingRegistrations->count() > 0)
        $('#pendingRegistrationsTable').DataTable({
            order: [[5, 'desc']], // Sort by date registered descending
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [
                { targets: -1, orderable: false } // Actions column is not sortable
            ]
        });
        @endif

        // Approve registration
        $(document).on('click', '.approve-btn', function() {
            const registrationId = $(this).data('registration-id');
            const registrationName = $(this).data('registration-name');

            Swal.fire({
                title: 'Approve Registration?',
                html: `Are you sure you want to approve the registration for <strong>${registrationName}</strong>?<br><br>This will activate the account and allow the user to login.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Yes, Approve',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we approve the registration.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    axios.post(`/admin/pending-registrations/${registrationId}/approve`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: response.data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.response?.data?.message || 'An error occurred while approving the registration.',
                            });
                        });
                }
            });
        });

        // Disapprove registration
        $(document).on('click', '.disapprove-btn', function() {
            const registrationId = $(this).data('registration-id');
            const registrationName = $(this).data('registration-name');

            Swal.fire({
                title: 'Disapprove Registration?',
                html: `Are you sure you want to disapprove the registration for <strong>${registrationName}</strong>?`,
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Rejection Reason (Optional)',
                inputPlaceholder: 'Enter reason for rejection...',
                inputAttributes: {
                    'aria-label': 'Enter reason for rejection'
                },
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-times mr-2"></i>Yes, Disapprove',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                inputValidator: (value) => {
                    // Optional field, no validation needed
                    return null;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const rejectionReason = result.value || '';

                    // Show loading
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process the disapproval.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    axios.post(`/admin/pending-registrations/${registrationId}/disapprove`, {
                        rejection_reason: rejectionReason
                    })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Disapproved!',
                                    text: response.data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.response?.data?.message || 'An error occurred while disapproving the registration.',
                            });
                        });
                }
            });
        });
    });
</script>
@endpush

