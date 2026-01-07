@extends('admin.layout')

@section('title', 'Create Referendum')

@php
    $pageTitle = 'Create Referendum';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.referendums.index'),
        'text' => 'Back to Referendums',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .user-select-item {
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        margin: 0.25rem;
        display: inline-block;
        background: white;
    }
    .user-select-item.selected {
        background-color: #055498;
        color: white;
        border-color: #055498;
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create Referendum</h2>
        <p class="text-gray-600 mt-1">Create a new referendum post with voting and commenting features</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="createReferendumForm" class="space-y-6">
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Main Form Fields -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Enter referendum title"
                        >
                        <span class="text-red-500 text-sm hidden" id="title-error"></span>
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="8"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Enter referendum content/description"
                        ></textarea>
                        <span class="text-red-500 text-sm hidden" id="content-error"></span>
                    </div>

                    <!-- Attachments -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments *</label>
                        <div 
                            id="attachmentsDropZone" 
                            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors hover:border-[#055498] hover:bg-blue-50/50"
                        >
                            <input 
                                type="file" 
                                id="attachments" 
                                name="attachments[]" 
                                multiple
                                accept="image/*,.pdf,.doc,.docx"
                                class="hidden"
                            >
                            <div id="dropZoneContent" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="text-[#055498] font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">Images, PDF, DOC, DOCX (Max: 30MB per file)</p>
                            </div>
                            <div id="dropZoneActive" class="hidden">
                                <i class="fas fa-file-upload text-4xl text-[#055498] mb-3 animate-bounce"></i>
                                <p class="text-sm text-[#055498] font-semibold">Drop files here</p>
                            </div>
                            <div id="attachmentsPreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                        </div>
                        <span class="text-red-500 text-sm hidden" id="attachments-error"></span>
                    </div>

                    <!-- Expiration Date & Time -->
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">Expiration Date & Time *</label>
                        <input 
                            type="text" 
                            id="expires_at" 
                            name="expires_at" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                            placeholder="Select expiration date and time"
                        >
                        <span class="text-red-500 text-sm hidden" id="expires_at-error"></span>
                        <p class="text-xs text-gray-500 mt-1">Voting and commenting will be disabled after this date/time</p>
                    </div>
                </div>

                <!-- Right Column: Allowed Users -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Users *</label>
                        <p class="text-xs text-gray-500 mb-3">Select users who can view, vote, and comment on this referendum</p>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-[calc(100vh-300px)] overflow-y-auto">
                    <div class="mb-3">
                        <input 
                            type="text" 
                            id="userSearch" 
                            placeholder="Search users..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                        >
                    </div>
                    <div class="mb-3 pb-3 border-b border-gray-200">
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer font-medium">
                            <input 
                                type="checkbox" 
                                id="selectAllUsers"
                                class="h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498]"
                            >
                            <span class="ml-3 text-sm text-gray-700">Select All</span>
                        </label>
                    </div>
                    <div id="usersList" class="space-y-2">
                        @php
                            $currentPrivilege = null;
                            $currentRepresentativeType = null;
                        @endphp
                        @foreach($users as $user)
                            @if($currentPrivilege !== $user->privilege)
                                @if($currentPrivilege !== null)
                                    </div>
                                @endif
                                <div class="mb-2 mt-3 first:mt-0">
                                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                        @if($user->privilege === 'user')
                                            Board Members
                                        @elseif($user->privilege === 'consec')
                                            CONSEC Accounts
                                        @else
                                            {{ ucfirst($user->privilege ?? 'Other') }}
                                        @endif
                                    </h5>
                                </div>
                                <div class="space-y-2">
                                @php
                                    $currentPrivilege = $user->privilege;
                                    $currentRepresentativeType = null;
                                @endphp
                            @endif
                            
                            @if($user->privilege === 'user' && $currentRepresentativeType !== $user->representative_type)
                                @if($currentRepresentativeType !== null)
                                    </div>
                                @endif
                                <div class="ml-4 mb-1 mt-2">
                                    <h6 class="text-xs font-medium text-gray-600 uppercase tracking-wide">
                                        @if($user->representative_type === 'Board Member')
                                            Board Members
                                        @elseif($user->representative_type === 'Authorized Representative')
                                            Authorized Representatives
                                        @else
                                            {{ $user->representative_type ?? 'Other' }}
                                        @endif
                                    </h6>
                                </div>
                                <div class="ml-4 space-y-2">
                                @php
                                    $currentRepresentativeType = $user->representative_type;
                                @endphp
                            @endif
                            
                            @php
                                $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=64&background=055498&color=fff';
                                if ($user->profile_picture) {
                                    $media = \App\Models\MediaLibrary::find($user->profile_picture);
                                    if ($media) {
                                        $profilePic = asset('storage/' . $media->file_path);
                                    }
                                }
                            @endphp
                            <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer user-item {{ $user->privilege === 'user' ? 'ml-4' : '' }}">
                                <input 
                                    type="checkbox" 
                                    name="allowed_users[]" 
                                    value="{{ $user->id }}"
                                    class="user-checkbox h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498] flex-shrink-0"
                                >
                                <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2 flex-shrink-0" style="border-color: #055498;">
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-medium text-gray-700 block truncate">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </span>
                                    <span class="text-xs text-gray-500 block truncate">{{ $user->email }}</span>
                                    @if($user->governmentAgency)
                                        <span class="text-xs text-gray-400 block truncate">{{ $user->governmentAgency->name }}</span>
                                    @endif
                                </div>
                                @if($user->privilege === 'consec')
                                    <span class="ml-auto px-2 py-0.5 text-xs rounded font-medium flex-shrink-0" style="background-color: #055498; color: #ffffff;">CONSEC</span>
                                @endif
                            </label>
                        @endforeach
                        @if($currentPrivilege === 'user' && $currentRepresentativeType !== null)
                            </div>
                        @endif
                        @if($currentPrivilege !== null)
                            </div>
                        @endif
                    </div>
                        </div>
                        <span class="text-red-500 text-sm hidden" id="allowed_users-error"></span>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col space-y-3 mt-4 pt-4 border-t">
                            <button 
                                type="submit" 
                                id="submitBtn"
                                class="w-full px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
                                style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                            >
                                <span id="submitBtnText">Create Referendum</span>
                                <span id="submitBtnLoader" class="hidden"><i class="fas fa-spinner fa-spin mr-2"></i>Creating...</span>
                            </button>
                            <a 
                                href="{{ route('admin.referendums.index') }}" 
                                class="w-full px-6 py-3 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Configure SweetAlert Toast for top right notifications
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Initialize date/time picker
    flatpickr("#expires_at", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: new Date().fp_incr(1), // Minimum 1 hour from now
        time_24hr: true,
        defaultHour: 23,
        defaultMinute: 59
    });

    // User search filter
    $('#userSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.user-item').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        updateSelectAllState();
    });

    // Select All functionality
    function updateSelectAllState() {
        const visibleCheckboxes = $('.user-item:visible .user-checkbox');
        const checkedCheckboxes = $('.user-item:visible .user-checkbox:checked');
        const selectAllCheckbox = $('#selectAllUsers');
        
        if (visibleCheckboxes.length === 0) {
            selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
            return;
        }
        
        if (checkedCheckboxes.length === 0) {
            selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes.length === visibleCheckboxes.length) {
            selectAllCheckbox.prop('indeterminate', false).prop('checked', true);
        } else {
            selectAllCheckbox.prop('indeterminate', true).prop('checked', false);
        }
    }

    $('#selectAllUsers').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.user-item:visible .user-checkbox').prop('checked', isChecked);
    });

    // Update select all state when individual checkboxes change
    $(document).on('change', '.user-checkbox', function() {
        updateSelectAllState();
    });

    // Initialize select all state
    updateSelectAllState();

    // Store uploaded attachment IDs
    let uploadedAttachmentIds = [];

    // Handle file preview and upload
    const attachmentsInput = document.getElementById('attachments');
    const attachmentsPreview = document.getElementById('attachmentsPreview');
    const attachmentsDropZone = document.getElementById('attachmentsDropZone');
    const dropZoneContent = document.getElementById('dropZoneContent');
    const dropZoneActive = document.getElementById('dropZoneActive');

    // Click to upload
    dropZoneContent.addEventListener('click', () => {
        attachmentsInput.click();
    });

    // Drag and drop handlers
    attachmentsDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        attachmentsDropZone.classList.add('border-[#055498]', 'bg-blue-50/50');
        dropZoneContent.classList.add('hidden');
        dropZoneActive.classList.remove('hidden');
    });

    attachmentsDropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        attachmentsDropZone.classList.remove('border-[#055498]', 'bg-blue-50/50');
        dropZoneContent.classList.remove('hidden');
        dropZoneActive.classList.add('hidden');
    });

    attachmentsDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        attachmentsDropZone.classList.remove('border-[#055498]', 'bg-blue-50/50');
        dropZoneContent.classList.remove('hidden');
        dropZoneActive.classList.add('hidden');

        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            handleFilesUpload(files);
        }
    });

    // Handle file upload function
    async function handleFilesUpload(files) {
        if (files.length === 0) return;

        // Show loading state
        if (uploadedAttachmentIds.length === 0) {
            attachmentsPreview.innerHTML = '<div class="col-span-full text-center py-4"><i class="fas fa-spinner fa-spin"></i> Uploading files...</div>';
        } else {
            attachmentsPreview.insertAdjacentHTML('beforeend', '<div class="col-span-full text-center py-4"><i class="fas fa-spinner fa-spin"></i> Uploading additional files...</div>');
        }

        // Upload files to media library
        const uploadFormData = new FormData();
        files.forEach(file => {
            uploadFormData.append('files[]', file);
        });

        try {
            const uploadResponse = await axios.post('{{ route("admin.media-library.store") }}', uploadFormData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (uploadResponse.data.success && uploadResponse.data.files) {
                // Add new IDs to existing ones
                const newIds = uploadResponse.data.files.map(file => file.id);
                uploadedAttachmentIds = [...uploadedAttachmentIds, ...newIds];
                
                // Remove loading message
                const loadingMsg = attachmentsPreview.querySelector('.col-span-full.text-center');
                if (loadingMsg) {
                    loadingMsg.remove();
                }
                
                // Clear error state when files are uploaded
                $('#attachments-error').addClass('hidden');
                attachmentsDropZone.classList.remove('border-red-500');
                
                // Display previews for new files
                uploadResponse.data.files.forEach(file => {
                    const isImage = file.type.startsWith('image/');
                    const previewHtml = `
                        <div class="relative border rounded-lg p-2 attachment-item" data-file-id="${file.id}">
                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors delete-attachment-btn" data-file-id="${file.id}" data-file-name="${file.name}" title="Remove attachment">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                            ${isImage ? 
                                `<img src="${file.url}" alt="${file.name}" class="w-full h-24 object-cover rounded">` :
                                `<div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
                                    <i class="fas fa-file text-3xl text-gray-400"></i>
                                </div>`
                            }
                            <p class="text-xs text-gray-600 mt-1 truncate" title="${file.name}">${file.name}</p>
                        </div>
                    `;
                    attachmentsPreview.insertAdjacentHTML('beforeend', previewHtml);
                });
            } else {
                const errorMsg = uploadResponse.data?.message || 'Failed to upload files. Please try again.';
                attachmentsPreview.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">' + errorMsg + '</div>';
            }
        } catch (error) {
            const errorMsg = error.response?.data?.message || error.message || 'Error uploading files';
            attachmentsPreview.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">' + errorMsg + '</div>';
            console.error('Upload error:', error);
        }
    }

    // Handle file input change
    attachmentsInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            await handleFilesUpload(files);
            // Reset input to allow selecting same files again
            attachmentsInput.value = '';
        }
    });

    // Delete attachment handler with SweetAlert
    $(document).on('click', '.delete-attachment-btn', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const fileId = $(this).data('file-id');
        const attachmentItem = $(this).closest('.attachment-item');
        const fileName = $(this).data('file-name') || attachmentItem.find('p').text().trim();

        const result = await Swal.fire({
            title: 'Remove Attachment?',
            text: `Are you sure you want to remove "${fileName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            // Remove from uploadedAttachmentIds array
            uploadedAttachmentIds = uploadedAttachmentIds.filter(id => id !== fileId);
            
            // Remove from preview with animation
            attachmentItem.fadeOut(300, function() {
                $(this).remove();
                
                // Show message if no attachments left
                if (uploadedAttachmentIds.length === 0 && attachmentsPreview.children.length === 0) {
                    // Drop zone is already visible, no need to show message
                }
            });

            Toast.fire({
                icon: 'success',
                title: 'Attachment removed successfully'
            });
        }
        
        return false;
    });

    // Form submission
    document.getElementById('createReferendumForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnLoader = document.getElementById('submitBtnLoader');

        // Clear previous errors
        document.querySelectorAll('.text-red-500').forEach(el => {
            el.classList.add('hidden');
        });

        // Validate at least one user is selected
        const selectedUsers = $('.user-checkbox:checked').length;
        if (selectedUsers === 0) {
            $('#allowed_users-error').text('Please select at least one user.').removeClass('hidden');
            return;
        }

        // Validate at least one attachment is uploaded
        if (uploadedAttachmentIds.length === 0) {
            $('#attachments-error').text('Please upload at least one attachment.').removeClass('hidden');
            attachmentsDropZone.classList.add('border-red-500');
            attachmentsDropZone.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        // Disable button
        submitBtn.disabled = true;
        submitBtnText.classList.add('hidden');
        submitBtnLoader.classList.remove('hidden');

        const formData = new FormData();
        formData.append('title', document.getElementById('title').value.trim());
        formData.append('content', document.getElementById('content').value.trim());
        formData.append('expires_at', document.getElementById('expires_at').value);
        
        // Get selected users
        const allowedUsers = [];
        $('.user-checkbox:checked').each(function() {
            allowedUsers.push($(this).val());
        });
        allowedUsers.forEach(userId => {
            formData.append('allowed_users[]', userId);
        });

        // Add uploaded attachment IDs
        if (uploadedAttachmentIds.length > 0) {
            uploadedAttachmentIds.forEach(id => {
                formData.append('attachments[]', id);
            });
        }
        
        try {
            const response = await axios.post('{{ route("admin.referendums.store") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.data.success || response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Referendum created successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '{{ route("admin.referendums.index") }}';
                });
            }
        } catch (error) {
            submitBtn.disabled = false;
            submitBtnText.classList.remove('hidden');
            submitBtnLoader.classList.add('hidden');

            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                
                Object.keys(errors).forEach(field => {
                    const errorElement = document.getElementById(field + '-error');
                    if (errorElement) {
                        errorElement.textContent = errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please check the form for errors.',
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

