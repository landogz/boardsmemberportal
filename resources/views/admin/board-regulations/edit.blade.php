@extends('admin.layout')

@section('title', 'Edit Board Regulation')

@php
    $pageTitle = 'Edit Board Regulation';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.board-regulations.index'),
        'text' => 'Back to Board Regulations',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Board Regulation</h2>
        <p class="text-gray-600 mt-1">Update regulation information</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="editRegulationForm" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ $regulation->title }}"
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
                >{{ $regulation->description }}</textarea>
                <span class="text-red-500 text-sm hidden" id="description-error"></span>
            </div>

            <input type="hidden" id="version" name="version" value="{{ $regulation->version }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="approved_date" class="block text-sm font-medium text-gray-700 mb-2">Approved Date *</label>
                    <input 
                        type="date" 
                        id="approved_date" 
                        name="approved_date" 
                        required
                        value="{{ $regulation->approved_date ? $regulation->approved_date->format('Y-m-d') : '' }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Date the Board approved this regulation (not the effectivity date).</p>
                    <span class="text-red-500 text-sm hidden" id="approved_date-error"></span>
                </div>

                <div>
                    <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">Effectivity Date *</label>
                    <input 
                        type="date" 
                        id="effective_date" 
                        name="effective_date" 
                        required
                        value="{{ $regulation->effective_date ? $regulation->effective_date->format('Y-m-d') : '' }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Date the regulation takes legal effect (typically 15 days after publication).</p>
                    <span class="text-red-500 text-sm hidden" id="effective_date-error"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="publication_date" class="block text-sm font-medium text-gray-700 mb-2">Publication Date (optional helper)</label>
                    <input 
                        type="date" 
                        id="publication_date" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">Enter publication date to suggest effectivity (+15 days). This field is not saved.</p>
                    <button type="button" id="applyPublicationEffectivity" class="mt-2 text-sm text-[#055498] hover:underline font-medium">Apply suggested effectivity date</button>
                </div>

                <div>
                    <label for="notice_id" class="block text-sm font-medium text-gray-700 mb-2">Notice of Meeting</label>
                    <select
                        id="notice_id"
                        name="notice_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    >
                        <option value="">— Select Notice of Meeting (optional) —</option>
                        @foreach($noticeOfMeetingNotices ?? [] as $nom)
                            <option value="{{ $nom->id }}" {{ (isset($regulation->notice_id) && $regulation->notice_id == $nom->id) ? 'selected' : '' }}>{{ $nom->title }}{{ $nom->meeting_date ? ' (' . $nom->meeting_date->format('M d, Y') . ')' : '' }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Optionally link this regulation to a Notice of Meeting.</p>
                </div>
            </div>

            <div>
                <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">PDF File</label>
                @if($regulation->pdf)
                    <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-700">Current PDF: <a href="{{ asset('storage/' . $regulation->pdf->file_path) }}" class="text-[#055498] hover:underline" data-pdf-title="{{ $regulation->pdf->file_name }}">{{ $regulation->pdf->file_name }}</a></p>
                    </div>
                @endif
                <input 
                    type="file" 
                    id="pdf_file" 
                    name="pdf_file" 
                    accept=".pdf"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#055498] file:text-white hover:file:bg-[#123a60] file:cursor-pointer cursor-pointer"
                >
                <span class="text-red-500 text-sm hidden" id="pdf_file-error"></span>
                <p class="text-xs text-gray-500 mt-1">Accepted format: PDF (Max: 100MB). Leave empty to keep current file.</p>
            </div>

            <div>
                <label for="change_notes" class="block text-sm font-medium text-gray-700 mb-2">Change Notes (Optional)</label>
                <textarea 
                    id="change_notes" 
                    name="change_notes" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                    placeholder="Optional: Add notes about what changed in this update (e.g., 'Updated policy section 3.2', 'Fixed typo in title')"
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">This note will be saved for reference.</p>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.board-regulations.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-white rounded-lg font-semibold transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    Update Regulation
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

    function addDaysToDate(dateStr, days) {
        if (!dateStr) return '';
        const parts = dateStr.split('-').map(Number);
        const date = new Date(parts[0], parts[1] - 1, parts[2]);
        date.setDate(date.getDate() + days);
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    $('#applyPublicationEffectivity').on('click', function() {
        const publicationDate = $('#publication_date').val();
        if (!publicationDate) {
            Swal.fire({ icon: 'warning', title: 'Publication date required', text: 'Enter a publication date to calculate the suggested effectivity date.' });
            return;
        }
        $('#effective_date').val(addDaysToDate(publicationDate, 15));
    });

    $('#editRegulationForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        $('.text-red-500').addClass('hidden').text('');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Updating...');

        axios.post('{{ route("admin.board-regulations.update", $regulation->id) }}', formData, {
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

