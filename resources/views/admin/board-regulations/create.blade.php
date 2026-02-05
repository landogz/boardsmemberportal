@extends('admin.layout')

@section('title', 'Create Board Regulation')

@php
    $pageTitle = 'Create Board Regulation';
    $headerActions = [];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Create Board Regulation</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Add a new board regulation to the system</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="createRegulationForm" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="Enter regulation title"
                >
                <span class="text-red-500 text-sm hidden" id="title-error"></span>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="Enter regulation description"
                ></textarea>
                <span class="text-red-500 text-sm hidden" id="description-error"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="version" class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                    <input 
                        type="text" 
                        id="version" 
                        name="version" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        placeholder="e.g., {{ date('Y') }}.01"
                        value="{{ old('version', date('Y') . '.01') }}"
                    >
                </div>

                <div>
                    <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">Effective Date *</label>
                    <input 
                        type="date" 
                        id="effective_date" 
                        name="effective_date" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        value="{{ old('effective_date', '2025-12-16') }}"
                    >
            </div>

            <div>
                    <label for="approved_date" class="block text-sm font-medium text-gray-700 mb-2">Approved Date *</label>
                <input 
                    type="date" 
                    id="approved_date" 
                    name="approved_date" 
                        required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        placeholder="mm/dd/yyyy"
                        value="{{ old('approved_date') }}"
                >
                <p class="text-xs text-gray-500 mt-1">If left empty, approved date will default to effective date.</p>
                </div>
            </div>

            <div>
                <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">PDF File *</label>
                <input 
                    type="file" 
                    id="pdf_file" 
                    name="pdf_file" 
                    required
                    accept=".pdf"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#055498] file:text-white hover:file:bg-[#123a60] file:cursor-pointer cursor-pointer"
                >
                <span class="text-red-500 text-sm hidden" id="pdf_file-error"></span>
                <p class="text-xs text-gray-500 mt-1">Accepted format: PDF (Max: 30MB)</p>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.board-regulations.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    Create Regulation
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

    $('#createRegulationForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        $('.text-red-500').addClass('hidden').text('');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating...');

        axios.post('{{ route("admin.board-regulations.store") }}', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
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
                    window.location.href = response.data.redirect;
                });
            }
        })
        .catch(error => {
            submitBtn.prop('disabled', false).html(originalText);

            if (error.response?.data?.errors) {
                Object.keys(error.response.data.errors).forEach(field => {
                    const errorMsg = error.response.data.errors[field][0];
                    $(`#${field}-error`).removeClass('hidden').text(errorMsg);
                });
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'An error occurred. Please try again.'
            });
        });
    });
</script>
@endpush

