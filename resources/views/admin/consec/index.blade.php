@extends('admin.layout')

@section('title', 'CONSEC Accounts')

@php
    $pageTitle = 'CONSEC Accounts';
    $headerActions = [];
    if (Auth::user()->hasPermission('create consec accounts')) {
        $headerActions[] = [
            'url' => route('admin.consec.create'),
            'text' => 'Add New CONSEC Account',
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
    .permission-matrix {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .permission-matrix thead th {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .permission-matrix tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .permission-matrix td,
    .permission-matrix th {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .permission-matrix .action-cell {
        font-weight: 500;
        color: #374151;
        min-width: 250px;
    }
    
    .permission-matrix .role-cell {
        text-align: center;
        width: 120px;
    }
    
    .permission-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #055498;
    }
    
    .category-header {
        background-color: #f3f4f6;
        font-weight: 600;
        color: #1f2937;
        cursor: pointer;
        user-select: none;
    }
    
    .category-header:hover {
        background-color: #e5e7eb;
    }
    
    .category-header .category-icon {
        margin-right: 8px;
        color: #055498;
    }
    
    .category-header .expand-icon {
        float: right;
        margin-top: 2px;
        color: #6b7280;
        transition: transform 0.2s ease;
    }
    
    .category-header.collapsed .expand-icon {
        transform: rotate(-90deg);
    }
    
    .category-header:not(.collapsed) .expand-icon {
        transform: rotate(0deg);
    }
    
    .permission-row {
        display: table-row;
    }
    
    .permission-row.hidden {
        display: none;
    }
    
    .permission-name {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .permission-matrix .permission-name {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .permission-toggle {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #055498;
    }
    
    .expand-icon {
        transition: transform 0.2s ease;
    }
    
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
    #consecTable td {
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
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Manage CONSEC Accounts</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">View and manage all CONSEC accounts</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 lg:p-6">
            <div class="overflow-x-auto">
                <table id="consecTable" class="min-w-full divide-y divide-gray-200">
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
                        @foreach($consecAccounts as $account)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $profileMedia = $account->profile_picture ? \App\Models\MediaLibrary::find($account->profile_picture) : null;
                                            $profileUrl = $profileMedia ? asset('storage/' . $profileMedia->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($account->first_name . ' ' . $account->last_name) . '&size=40&background=055498&color=fff';
                                        @endphp
                                        <img src="{{ $profileUrl }}" alt="Profile" class="h-10 w-10 rounded-full object-cover border-2" style="border-color: #055498;">
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $account->pre_nominal_title }} {{ $account->first_name }} {{ $account->middle_initial ? $account->middle_initial . '.' : '' }} {{ $account->last_name }} {{ $account->post_nominal_title ? ', ' . $account->post_nominal_title : '' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $account->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $account->username }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $account->designation ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($account->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="relative inline-block text-left" style="position: relative;">
                                    <button type="button" class="action-dropdown-btn inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#055498]" data-account-id="{{ $account->id }}">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div class="action-dropdown-menu hidden w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" data-dropdown-id="{{ $account->id }}">
                                        <div class="py-1" role="menu">
                                            @can('manage consec accounts')
                                            <button type="button" class="setup-permission-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900 flex items-center" role="menuitem" data-account-id="{{ $account->id }}" data-account-name="{{ $account->pre_nominal_title }} {{ $account->first_name }} {{ $account->middle_initial ? $account->middle_initial . '.' : '' }} {{ $account->last_name }} {{ $account->post_nominal_title ? ', ' . $account->post_nominal_title : '' }}" data-account-email="{{ $account->email }}">
                                                <i class="fas fa-key w-4 mr-3 text-purple-600"></i>
                                                Setup Permission
                                            </button>
                                            @endcan
                                            @can('edit consec accounts')
                                            <a href="{{ route('admin.consec.edit', $account->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 flex items-center" role="menuitem">
                                                <i class="fas fa-edit w-4 mr-3 text-blue-600"></i>
                                                Edit Account
                                            </a>
                                            @php
                                                $toggleText = $account->is_active ? 'Deactivate Account' : 'Activate Account';
                                                $toggleIcon = $account->is_active ? 'fa-ban' : 'fa-check-circle';
                                                $toggleColor = $account->is_active ? 'text-yellow-600' : 'text-green-600';
                                            @endphp
                                            <button type="button" class="toggle-status-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center" role="menuitem" data-account-id="{{ $account->id }}" data-is-active="{{ $account->is_active ? 1 : 0 }}">
                                                <i class="fas {{ $toggleIcon }} w-4 mr-3 {{ $toggleColor }}"></i>
                                                {{ $toggleText }}
                                            </button>
                                            @endcan
                                            @can('delete consec accounts')
                                            <button type="button" class="delete-account-btn w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center" role="menuitem" data-account-id="{{ $account->id }}">
                                                <i class="fas fa-trash w-4 mr-3"></i>
                                                Delete Account
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($consecAccounts->count() === 0)
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-users-cog text-4xl mb-4"></i>
                        <p class="text-lg font-medium">No CONSEC accounts found</p>
                        <p class="text-sm mt-2">Get started by creating your first CONSEC account</p>
                        <a href="{{ route('admin.consec.create') }}" class="mt-4 inline-block px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            <i class="fas fa-plus mr-2"></i>Add New CONSEC Account
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Permission Setup Modal -->
<div id="permissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">User Permissions</h3>
            <button type="button" id="closePermissionModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <!-- User Info -->
        <div class="flex items-center mb-6 pb-4 border-b">
            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                <i class="fas fa-user text-gray-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-semibold text-gray-800" id="modalUserName"></h4>
                <p class="text-sm text-gray-600" id="modalUserEmail"></p>
            </div>
            <a href="#" id="modalViewProfile" class="text-blue-600 hover:text-blue-800 text-sm">
                View profile <i class="fas fa-external-link-alt ml-1"></i>
            </a>
        </div>

        <!-- Permissions List -->
        <div class="mb-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                Check/uncheck boxes to grant or revoke permissions for this user
            </div>
        </div>
        
        <div class="overflow-x-auto border border-gray-300 rounded-lg">
            <div class="max-h-96 overflow-y-auto" id="permissionsContainer">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Loading permissions...</p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4 border-t">
            <button type="button" id="savePermissionsBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                Save changes
            </button>
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
        @if($consecAccounts->count() > 0)
        $('#consecTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [
                { targets: -1, orderable: false } // Actions column is not sortable
            ]
        });
        @endif
    });

    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Dropdown menu toggle
    $(document).on('click', '.action-dropdown-btn', function(e) {
        e.stopPropagation();
        const accountId = $(this).data('account-id');
        const $dropdown = $(`.action-dropdown-menu[data-dropdown-id="${accountId}"]`);
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

    // Toggle status event listener
    $(document).on('click', '.toggle-status-btn', function() {
        const id = $(this).data('account-id');
        const isActive = $(this).data('is-active');
        
        // isActive is 1 or 0
        const isActiveBool = isActive === 1 || isActive === '1';
        const action = isActiveBool ? 'deactivate' : 'activate';
        const actionText = isActiveBool ? 'Deactivate' : 'Activate';
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to ' + action + ' this CONSEC account?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActiveBool ? '#F59E0B' : '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, ' + actionText + '!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post('/admin/consec/' + id + '/toggle-status')
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
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
    });

    // Delete account event listener
    $(document).on('click', '.delete-account-btn', function() {
        const id = $(this).data('account-id');
        
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
                axios.delete('/admin/consec/' + id)
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
    });

    // Setup Permission Modal
    let currentUserId = null;
    let currentUserPermissions = [];

    $(document).on('click', '.setup-permission-btn', function(e) {
        e.preventDefault();
        const accountId = $(this).data('account-id');
        const accountName = $(this).data('account-name');
        const accountEmail = $(this).data('account-email');
        
        currentUserId = accountId;
        $('#modalUserName').text(accountName);
        $('#modalUserEmail').text(accountEmail);
        $('#modalViewProfile').attr('href', '/admin/consec/' + accountId + '/edit');
        $('#permissionModal').removeClass('hidden');
        
        // Load permissions
        loadUserPermissions(accountId);
    });

    $('#closePermissionModal').on('click', function() {
        $('#permissionModal').addClass('hidden');
    });

    // Close modal when clicking outside
    $('#permissionModal').on('click', function(e) {
        if ($(e.target).attr('id') === 'permissionModal') {
            $('#permissionModal').addClass('hidden');
        }
    });

    function loadUserPermissions(userId) {
        $('#permissionsContainer').html('<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i><p class="text-gray-500">Loading permissions...</p></div>');
        
        axios.get('/admin/consec/' + userId + '/permissions')
            .then(response => {
                if (response.data.success) {
                    // Convert all permission IDs to numbers for consistent comparison
                    currentUserPermissions = response.data.user_permissions.map(id => parseInt(id));
                    renderPermissions(response.data.permissions, currentUserPermissions);
                }
            })
            .catch(error => {
                $('#permissionsContainer').html('<div class="text-center py-8 text-red-500">Error loading permissions. Please try again.</div>');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'An error occurred while loading permissions.'
                });
            });
    }

    function getPermissionDescription(permissionName) {
        const descriptions = {
            'view users': 'Allows the user to view user accounts',
            'create users': 'Allows the user to create new user accounts',
            'edit users': 'Allows the user to edit existing user accounts',
            'delete users': 'Allows the user to delete user accounts',
            'manage users': 'Allows the user to manage all user-related operations',
            'view roles': 'Allows the user to view roles',
            'create roles': 'Allows the user to create new roles',
            'edit roles': 'Allows the user to edit existing roles',
            'delete roles': 'Allows the user to delete roles',
            'manage roles': 'Allows the user to manage all role-related operations',
            'view permissions': 'Allows the user to view permissions',
            'create permissions': 'Allows the user to create new permissions',
            'edit permissions': 'Allows the user to edit existing permissions',
            'delete permissions': 'Allows the user to delete permissions',
            'manage permissions': 'Allows the user to manage all permission-related operations',
            'view board resolutions': 'Allows the user to view board resolutions',
            'create board resolutions': 'Allows the user to create new board resolutions',
            'edit board resolutions': 'Allows the user to edit existing board resolutions',
            'delete board resolutions': 'Allows the user to delete board resolutions',
            'manage board resolutions': 'Allows the user to manage all board resolution operations',
            'view board regulations': 'Allows the user to view board regulations',
            'create board regulations': 'Allows the user to create new board regulations',
            'edit board regulations': 'Allows the user to edit existing board regulations',
            'delete board regulations': 'Allows the user to delete board regulations',
            'manage board regulations': 'Allows the user to manage all board regulation operations',
            'view government agencies': 'Allows the user to view government agencies',
            'create government agencies': 'Allows the user to create new government agencies',
            'edit government agencies': 'Allows the user to edit existing government agencies',
            'delete government agencies': 'Allows the user to delete government agencies',
            'manage government agencies': 'Allows the user to manage all government agency operations',
            'view media library': 'Allows the user to view media library items',
            'upload media': 'Allows the user to upload media files',
            'edit media': 'Allows the user to edit media library items',
            'delete media': 'Allows the user to delete media library items',
            'manage media library': 'Allows the user to manage all media library operations',
            'view announcements': 'Allows the user to view announcements',
            'create announcements': 'Allows the user to create new announcements',
            'edit announcements': 'Allows the user to edit existing announcements',
            'delete announcements': 'Allows the user to delete announcements',
            'manage announcements': 'Allows the user to manage all announcement operations',
            'view notices': 'Allows the user to view notices',
            'create notices': 'Allows the user to create new notices',
            'edit notices': 'Allows the user to edit existing notices',
            'delete notices': 'Allows the user to delete notices',
            'manage notices': 'Allows the user to manage all notice operations',
            'view calendar events': 'Allows the user to view calendar events',
            'create calendar events': 'Allows the user to create new calendar events',
            'edit calendar events': 'Allows the user to edit existing calendar events',
            'delete calendar events': 'Allows the user to delete calendar events',
            'manage calendar events': 'Allows the user to manage all calendar event operations',
            'view audit logs': 'Allows the user to view audit logs',
            'manage consec accounts': 'Allows the user to manage CONSEC accounts',
        };
        
        return descriptions[permissionName.toLowerCase()] || 'Allows the user to perform this action';
    }

    // Simple hash function for category IDs (similar to PHP md5)
    function md5(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString();
    }

    function renderPermissions(groupedPermissions, userPermissions) {
        let html = '<table class="permission-matrix min-w-full">';
        html += '<thead><tr><th class="action-cell">Actions</th><th class="role-cell">Select</th></tr></thead>';
        html += '<tbody>';
        
        Object.keys(groupedPermissions).forEach((category, index) => {
            const group = groupedPermissions[category];
            const categoryId = md5(category);
            
            html += `
                <tr class="category-header" data-category="${categoryId}">
                    <td colspan="2" class="py-3 px-4 cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="${group.icon} category-icon"></i>
                                <span class="font-semibold">${category}</span>
                                <span class="text-xs text-gray-500 ml-2">(${group.permissions.length} permission${group.permissions.length !== 1 ? 's' : ''})</span>
                            </div>
                            <i class="fas fa-chevron-down expand-icon"></i>
                        </div>
                    </td>
                </tr>
            `;
            
            group.permissions.forEach(permission => {
                const permissionId = parseInt(permission.id);
                const isChecked = userPermissions.includes(permissionId);
                const permissionName = permission.name;
                const capitalizedName = permissionName.split(' ').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
                
                html += `
                    <tr class="permission-row" data-category="${categoryId}">
                        <td class="action-cell">
                            <div class="permission-name pl-6">${capitalizedName}</div>
                        </td>
                        <td class="role-cell">
                            <input 
                                type="checkbox" 
                                class="permission-checkbox permission-toggle" 
                                value="${permissionId}" 
                                ${isChecked ? 'checked' : ''}
                            >
                        </td>
                    </tr>
                `;
            });
        });
        
        html += '</tbody></table>';
        $('#permissionsContainer').html(html);
        
        // Initialize: Expand first category, collapse others
        $('.category-header').each(function(index) {
            const categoryId = $(this).data('category');
            const $rows = $(`.permission-row[data-category="${categoryId}"]`);
            
            if (index === 0) {
                // First category expanded by default
                $(this).removeClass('collapsed');
                $rows.removeClass('hidden').show();
            } else {
                // Other categories collapsed by default
                $(this).addClass('collapsed');
                $rows.addClass('hidden').hide();
            }
        });
        
        // Handle category expand/collapse
        $('.category-header').off('click').on('click', function() {
            const categoryId = $(this).data('category');
            const $header = $(this);
            const $rows = $(`.permission-row[data-category="${categoryId}"]`);
            const $icon = $header.find('.expand-icon');
            
            $header.toggleClass('collapsed');
            
            if ($header.hasClass('collapsed')) {
                $rows.addClass('hidden').hide();
                $icon.css('transform', 'rotate(-90deg)');
            } else {
                $rows.removeClass('hidden').show();
                $icon.css('transform', 'rotate(0deg)');
            }
        });
    }

    $('#savePermissionsBtn').on('click', function() {
        const selectedPermissions = [];
        $('.permission-toggle:checked').each(function() {
            // Convert to integer to ensure proper type
            selectedPermissions.push(parseInt($(this).val()));
        });
        
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
        
        axios.post('/admin/consec/' + currentUserId + '/permissions', {
            permissions: selectedPermissions
        })
        .then(response => {
            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $('#permissionModal').addClass('hidden');
                    window.location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'An error occurred while saving permissions.'
            });
        })
        .finally(() => {
            btn.prop('disabled', false).html(originalText);
        });
    });
</script>
@endpush

