@extends('admin.layout')

@section('title', 'Admin Accounts')

@php
    $pageTitle = 'Admin Accounts';
    $headerActions = [
        [
            'url' => route('admin.admin-accounts.create'),
            'text' => 'Add New Admin Account',
            'icon' => 'fas fa-plus',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ],
    ];
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manage Admin Accounts</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">View and manage all admin users</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            <div class="overflow-x-auto">
                <table id="adminAccountsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($adminAccounts as $account)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $profileMedia = $account->profile_picture ? \App\Models\MediaLibrary::find($account->profile_picture) : null;
                                            $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($account->short_name) . '&size=40&background=055498&color=fff';
                                        @endphp
                                        <img src="{{ $profileUrl }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border-2" style="border-color: #055498;">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $account->full_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->username }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $account->designation ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($account->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.admin-accounts.edit', $account->id) }}" class="px-3 py-1.5 rounded border border-blue-200 text-blue-700 hover:bg-blue-50">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <button type="button" class="toggle-status-btn px-3 py-1.5 rounded border border-yellow-200 text-yellow-700 hover:bg-yellow-50" data-account-id="{{ $account->id }}" data-is-active="{{ $account->is_active ? 1 : 0 }}">
                                        <i class="fas {{ $account->is_active ? 'fa-ban' : 'fa-check-circle' }} mr-1"></i>{{ $account->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button type="button" class="delete-account-btn px-3 py-1.5 rounded border border-red-200 text-red-700 hover:bg-red-50" data-account-id="{{ $account->id }}">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($adminAccounts->count() === 0)
                <div class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-user-shield text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No admin accounts found</p>
                    <a href="{{ route('admin.admin-accounts.create') }}" class="mt-4 inline-block px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                        <i class="fas fa-plus mr-2"></i>Add New Admin Account
                    </a>
                </div>
                @endif
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
    $(document).ready(function() {
        @if($adminAccounts->count() > 0)
        $('#adminAccountsTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            columnDefs: [{ targets: -1, orderable: false }]
        });
        @endif
    });

    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    $(document).on('click', '.toggle-status-btn', function() {
        const id = $(this).data('account-id');
        const isActive = $(this).data('is-active') === 1 || $(this).data('is-active') === '1';
        const action = isActive ? 'deactivate' : 'activate';

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to ' + action + ' this admin account?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            confirmButtonColor: isActive ? '#F59E0B' : '#10B981'
        }).then((result) => {
            if (!result.isConfirmed) return;
            axios.post('/admin/admin-accounts/' + id + '/toggle-status')
                .then((response) => {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.data.message, timer: 1300, showConfirmButton: false })
                        .then(() => window.location.reload());
                })
                .catch((error) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: error.response?.data?.message || 'Failed to update status.' });
                });
        });
    });

    $(document).on('click', '.delete-account-btn', function() {
        const id = $(this).data('account-id');
        Swal.fire({
            title: 'Delete this admin account?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#EF4444'
        }).then((result) => {
            if (!result.isConfirmed) return;
            axios.delete('/admin/admin-accounts/' + id)
                .then((response) => {
                    Swal.fire({ icon: 'success', title: 'Deleted', text: response.data.message, timer: 1300, showConfirmButton: false })
                        .then(() => window.location.reload());
                })
                .catch((error) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: error.response?.data?.message || 'Failed to delete account.' });
                });
        });
    });
</script>
@endpush

