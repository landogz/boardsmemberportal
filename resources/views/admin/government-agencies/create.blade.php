@extends('admin.layout')

@section('title', 'Create Agency')

@php
    $pageTitle = 'Create Government Agency';
    $headerActions = [];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create Government Agency</h2>
        <p class="text-gray-600 mt-1">Add a new government agency to the system</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="createAgencyForm" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Agency Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="Enter agency name"
                >
                <span class="text-red-500 text-sm hidden" id="name-error"></span>
            </div>

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Agency Code</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="Enter agency code (e.g., DOH, DTI)"
                >
                <span class="text-red-500 text-sm hidden" id="code-error"></span>
                <p class="text-xs text-gray-500 mt-1">Optional: Short code or abbreviation for the agency</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="Enter agency description"
                ></textarea>
                <span class="text-red-500 text-sm hidden" id="description-error"></span>
            </div>

            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Agency Logo</label>
                <div class="mt-1 flex items-center space-x-5">
                    <div class="flex-1">
                        <input 
                            type="file" 
                            id="logo" 
                            name="logo" 
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#055498] file:text-white hover:file:bg-[#123a60] file:cursor-pointer cursor-pointer"
                            onchange="previewLogo(this)"
                        >
                        <span class="text-red-500 text-sm hidden" id="logo-error"></span>
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: JPEG, PNG, JPG, GIF, SVG (Max: 2MB)</p>
                    </div>
                    <div id="logoPreview" class="hidden">
                        <img id="logoPreviewImg" src="" alt="Logo preview" class="h-20 w-20 object-contain border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="is_active" 
                    name="is_active" 
                    checked
                    class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-[#055498] accent-[#055498]"
                >
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a 
                    href="{{ route('admin.government-agencies.index') }}" 
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                    style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #123a60 0%, #055498 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #055498 0%, #123a60 100%)'"
                >
                    <span id="submitBtnText">Create Agency</span>
                    <span id="submitBtnLoader" class="hidden">Creating...</span>
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
    // Set up axios defaults
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Preview logo
    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        const previewImg = document.getElementById('logoPreviewImg');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    }

    document.getElementById('createAgencyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnLoader = document.getElementById('submitBtnLoader');

        // Clear previous errors
        document.querySelectorAll('.text-red-500').forEach(el => {
            el.classList.add('hidden');
        });

        // Disable button
        submitBtn.disabled = true;
        submitBtnText.classList.add('hidden');
        submitBtnLoader.classList.remove('hidden');

        const formData = new FormData();
        formData.append('name', document.getElementById('name').value.trim());
        formData.append('code', document.getElementById('code').value.trim() || '');
        formData.append('description', document.getElementById('description').value.trim() || '');
        formData.append('is_active', document.getElementById('is_active').checked ? '1' : '0');
        
        const logoFile = document.getElementById('logo').files[0];
        if (logoFile) {
            formData.append('logo', logoFile);
        }

        try {
            const response = await axios.post('{{ route("admin.government-agencies.store") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '{{ route("admin.government-agencies.index") }}';
                });
            }
        } catch (error) {
            submitBtn.disabled = false;
            submitBtnText.classList.remove('hidden');
            submitBtnLoader.classList.add('hidden');

            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                const errorMessages = [];
                
                Object.keys(errors).forEach(field => {
                    const errorElement = document.getElementById(field + '-error');
                    if (errorElement) {
                        errorElement.textContent = errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                    // Collect all error messages for SweetAlert
                    errors[field].forEach(msg => {
                        errorMessages.push(`• ${field === 'logo' ? 'Logo: ' : ''}${msg}`);
                    });
                });

                // Show specific error message in SweetAlert
                const errorTitle = error.response.data.message || 'Validation Error';
                const errorText = errorMessages.length > 0 
                    ? errorMessages.join('\n') 
                    : 'Please check the form for errors';

                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: errorText.replace(/\n/g, '<br>'),
                    width: '500px'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'An error occurred. Please try again.',
                });
            }
        }
    });
</script>
@endpush

