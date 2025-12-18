@extends('admin.layout')

@section('title', 'Create Permission')

@php
    $pageTitle = 'Create Permission';
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Permission</h2>
        <p class="text-gray-600 mt-1">Add a new permission to the system</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="createPermissionForm">
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Permission Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="e.g., manage users, view reports"
                >
                <p class="text-xs text-gray-500 mt-1">Use lowercase with spaces or dots (e.g., "manage users" or "users.manage")</p>
                <span class="text-red-500 text-sm hidden" id="name-error"></span>
            </div>

            <div class="mb-6">
                <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-2">Guard Name</label>
                <input 
                    type="text" 
                    id="guard_name" 
                    name="guard_name" 
                    value="web"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="web"
                >
                <p class="text-xs text-gray-500 mt-1">Default: web</p>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    Create Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    $('#createPermissionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            name: $('#name').val(),
            guard_name: $('#guard_name').val() || 'web'
        };

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('Creating...');

        axios.post('{{ route("admin.permissions.store") }}', formData)
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

