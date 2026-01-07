@extends('admin.layout')

@section('title', 'Role & Permission Manager')

@php
    $pageTitle = 'Role & Permission Manager';
    $headerActions = [];
    if (Auth::user()->hasPermission('create roles')) {
        $headerActions[] = [
            'url' => route('admin.roles.create'),
            'text' => 'Add New Role',
            'icon' => 'fas fa-plus',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ];
    }
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .tab-button {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .tab-button.active {
        color: #055498;
        border-bottom-color: #055498;
    }
    
    .tab-button:hover {
        color: #055498;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }

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
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Role & Permission Manager</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Manage roles and their permissions</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-t-lg border border-gray-200 border-b-0">
        <div class="flex flex-wrap space-x-1 px-2 sm:px-4 pt-3 sm:pt-4 overflow-x-auto">
            <button 
                id="rolesTab" 
                class="tab-button active"
                onclick="switchTab('roles')"
            >
                <i class="fas fa-shield-alt mr-2"></i>Roles
            </button>
            <button 
                id="permissionsTab" 
                class="tab-button"
                onclick="switchTab('permissions')"
            >
                <i class="fas fa-key mr-2"></i>Permissions Matrix
            </button>
        </div>
    </div>

    <!-- Roles Tab Content -->
    <div id="rolesTabContent" class="tab-content active">
        <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 lg:p-6">
                @if($roles->isEmpty())
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-shield-alt text-4xl mb-4"></i>
                        <p class="text-lg font-medium">No roles found</p>
                        <p class="text-sm mt-2">Get started by creating your first role</p>
                        @can('create roles')
                        <a href="{{ route('admin.roles.create') }}" class="mt-4 inline-block px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                            <i class="fas fa-plus mr-2"></i>Add New Role
                        </a>
                        @endcan
                    </div>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($roles as $role)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($role->name) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $role->users()->count() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $role->permissions()->count() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @can('edit roles')
                                        <a 
                                            href="{{ route('admin.roles.edit', $role->id) }}" 
                                            class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors"
                                            title="Edit Role"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete roles')
                                        <button 
                                            onclick="deleteRole({{ $role->id }})" 
                                            class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors"
                                            title="Delete Role"
                                            {{ $role->users()->count() > 0 ? 'disabled' : '' }}
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Permissions Matrix Tab Content -->
    <div id="permissionsTabContent" class="tab-content">
        <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 lg:p-6">
                @if($roles->isEmpty())
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-key text-4xl mb-4"></i>
                        <p class="text-lg font-medium">No roles found</p>
                        <p class="text-sm mt-2">Create a role first to manage permissions</p>
                    </div>
                </div>
                @else
                <div class="mb-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Check/uncheck boxes to grant or revoke permissions for each role
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $roles->count() }}</span> roles | 
                        <span class="font-semibold">{{ $allPermissions->count() }}</span> permissions
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="permission-matrix min-w-full">
                        <thead>
                            <tr>
                                <th class="action-cell">Actions</th>
                                @foreach($roles as $role)
                                <th class="role-cell">
                                    <div class="font-semibold text-gray-800">{{ ucfirst($role->name) }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $role->users()->count() }} user(s)</div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedPermissions as $category => $group)
                            @php
                                // Show "manage" permissions for "Request for Inclusion in the Agenda" and "Media Library" categories
                                $filteredPermissions = ($category === 'Request for Inclusion in the Agenda' || $category === 'Media Library')
                                    ? collect($group['permissions'])  // Show all permissions including "manage" for these categories
                                    : collect($group['permissions'])->filter(function($permission) {
                                        return !str_starts_with(strtolower($permission->name), 'manage ');
                                    })->values();
                            @endphp
                            @if($filteredPermissions->count() > 0)
                            <tr class="category-header" data-category="{{ md5($category) }}">
                                <td colspan="{{ $roles->count() + 1 }}" class="py-3 px-4 cursor-pointer hover:bg-gray-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="{{ $group['icon'] }} category-icon"></i>
                                            <span class="font-semibold">{{ $category }}</span>
                                            <span class="text-xs text-gray-500 ml-2">({{ $filteredPermissions->count() }} permission{{ $filteredPermissions->count() !== 1 ? 's' : '' }})</span>
                                        </div>
                                        <i class="fas fa-chevron-down expand-icon"></i>
                                    </div>
                                </td>
                            </tr>
                            @foreach($filteredPermissions as $permission)
                            <tr class="permission-row" data-category="{{ md5($category) }}">
                                <td class="action-cell">
                                    <div class="permission-name pl-6">{{ ucwords($permission->name) }}</div>
                                </td>
                                @foreach($roles as $role)
                                <td class="role-cell">
                                    <input 
                                        type="checkbox" 
                                        class="permission-checkbox" 
                                        data-role-id="{{ $role->id }}"
                                        data-permission-id="{{ $permission->id }}"
                                        {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}
                                        {{ $role->name === 'admin' ? 'disabled' : '' }}
                                    >
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Tab switching function
    function switchTab(tab) {
        // Hide all tab contents
        $('.tab-content').removeClass('active');
        $('.tab-button').removeClass('active');
        
        // Show selected tab
        if (tab === 'roles') {
            $('#rolesTabContent').addClass('active');
            $('#rolesTab').addClass('active');
        } else if (tab === 'permissions') {
            $('#permissionsTabContent').addClass('active');
            $('#permissionsTab').addClass('active');
        }
    }

    // Delete role function
    function deleteRole(id) {
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
                axios.delete(`/admin/roles/${id}`)
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

    $(document).ready(function() {
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
        $('.category-header').on('click', function() {
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
        
        // Handle permission checkbox changes
        $('.permission-checkbox').on('change', function() {
            const checkbox = $(this);
            const roleId = checkbox.data('role-id');
            const permissionId = checkbox.data('permission-id');
            const granted = checkbox.is(':checked');
            
            // Disable checkbox during request
            checkbox.prop('disabled', true);
            
            axios.post(`/admin/roles/${roleId}/update-permission`, {
                permission_id: permissionId,
                granted: granted
            })
            .then(response => {
                if (response.data.success) {
                    // Show success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Permission updated successfully',
                        timer: 1000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .catch(error => {
                // Revert checkbox state on error
                checkbox.prop('checked', !granted);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'An error occurred. Please try again.'
                });
            })
            .finally(() => {
                // Re-enable checkbox
                checkbox.prop('disabled', false);
            });
        });
    });
</script>
@endpush
