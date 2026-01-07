@extends('admin.layout')

@section('title', 'Create Role')

@php
    $pageTitle = 'Create Role';
@endphp

@push('styles')
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
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Role</h2>
        <p class="text-gray-600 mt-1">Add a new role to the system</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="createRoleForm">
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="e.g., Manager, Editor"
                >
                <span class="text-red-500 text-sm hidden" id="name-error"></span>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                <div class="mb-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Check boxes to grant permissions for this role
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $permissions->count() }}</span> permissions
                    </div>
                </div>
                
                <div class="overflow-x-auto border border-gray-300 rounded-lg">
                    <table class="permission-matrix min-w-full">
                        <thead>
                            <tr>
                                <th class="action-cell">Actions</th>
                                <th class="role-cell">Select</th>
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
                                <td colspan="2" class="py-3 px-4 cursor-pointer hover:bg-gray-100">
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
                                <td class="role-cell">
                                    <input 
                                        type="checkbox" 
                                        class="permission-checkbox" 
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                    >
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    Create Role
                </button>
            </div>
        </form>
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
    });

    $('#createRoleForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            name: $('#name').val(),
            permissions: $('.permission-checkbox:checked').map(function() {
                return $(this).val();
            }).get()
        };

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('Creating...');

        axios.post('{{ route("admin.roles.store") }}', formData)
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.data.redirect;
                    });
                }
            })
            .catch(error => {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach(field => {
                        const errorElement = $(`#${field}-error`);
                        if (errorElement.length) {
                            errorElement.text(errors[field][0]).removeClass('hidden');
                        }
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form for errors',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.',
                    });
                }
            });
    });
</script>
@endpush
