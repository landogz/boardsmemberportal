@extends('admin.layout')

@section('title', 'Edit Announcement')

@php
    $pageTitle = 'Edit Announcement';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.announcements.index'),
        'text' => 'Back to Announcements',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- CKEditor 4.19.1 -->
<script src="https://cdn.ckeditor.com/4.19.1/full/ckeditor.js"></script>
<style>
    #bannerPreview {
        max-height: 300px;
        object-fit: cover;
    }
    .ck-editor__editable {
        min-height: 300px;
    }
    /* Hide CKEditor security warning notification */
    #cke_notifications_area_description,
    .cke_notifications_area {
        display: none !important;
        visibility: hidden !important;
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Announcement</h2>
        <p class="text-gray-600 mt-1">Update announcement details and settings</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form id="editAnnouncementForm" action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            required
                            value="{{ old('title', $announcement->title) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >
                        @error('title')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                        >{{ old('description', $announcement->description) }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image</label>
                        @if($announcement->bannerImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $announcement->bannerImage->file_path) }}" alt="Current banner" class="w-full max-h-64 object-cover rounded-lg">
                            </div>
                        @endif
                        <div 
                            id="bannerDropZone"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center transition-colors hover:border-[#055498] hover:bg-blue-50/50"
                        >
                            <input 
                                type="file" 
                                id="banner_image" 
                                name="banner_image" 
                                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                class="hidden"
                                onchange="previewBanner(this)"
                            >
                            <div id="bannerUploadArea" class="cursor-pointer">
                                <i class="fas fa-image text-4xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span class="text-[#055498] font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">JPG, PNG, GIF, WEBP (Max: 10MB)</p>
                            </div>
                            <div id="bannerPreviewContainer" class="hidden mt-4">
                                <img id="bannerPreview" src="" alt="Banner preview" class="w-full rounded-lg max-h-64 object-cover">
                                <button type="button" onclick="removeBanner()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <!-- Publish Box (WordPress style) -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900">Publish</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                                    <select 
                                        id="status" 
                                        name="status" 
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                                    >
                                        <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                </div>

                                <!-- Schedule Publish -->
                                <div>
                                    <label for="scheduled_at" class="block text-xs font-medium text-gray-700 mb-2">Schedule Publish</label>
                                    <input 
                                        type="text" 
                                        id="scheduled_at" 
                                        name="scheduled_at" 
                                        value="{{ old('scheduled_at', $announcement->scheduled_at ? $announcement->scheduled_at->format('Y-m-d H:i') : '') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none transition"
                                        placeholder="Select date and time"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to publish immediately</p>
                                </div>

                                <!-- Update Button -->
                                <div class="pt-4 border-t border-gray-200 space-y-2">
                                    <button 
                                        type="submit" 
                                        class="w-full px-4 py-2 text-white rounded font-semibold transition-all duration-300 hover:shadow-md"
                                        style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
                                    >
                                        Update
                                    </button>
                                    <a 
                                        href="{{ route('admin.announcements.index') }}" 
                                        class="block w-full px-4 py-2 text-center border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition text-sm"
                                    >
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Allowed Users -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Users *</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 max-h-[600px] overflow-y-auto">
                            <input 
                                type="text" 
                                id="userSearch" 
                                placeholder="Search users..." 
                                class="w-full px-3 py-2 mb-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                            >
                            
                            <!-- Select All -->
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
                            
                            <div class="space-y-1" id="usersList">
                                @php
                                    $currentPrivilege = null;
                                    $currentRepresentativeType = null;
                                @endphp
                                @foreach($users as $user)
                                    @php
                                        $profilePic = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) . '&size=64&background=055498&color=fff';
                                        if ($user->profile_picture) {
                                            $media = \App\Models\MediaLibrary::find($user->profile_picture);
                                            if ($media) {
                                                $profilePic = asset('storage/' . $media->file_path);
                                            }
                                        }
                                        
                                        if ($currentPrivilege !== $user->privilege) {
                                            if ($currentPrivilege !== null) {
                                                if ($currentRepresentativeType !== null) {
                                                    echo '</div>';
                                                }
                                                echo '</div>';
                                            }
                                            $currentPrivilege = $user->privilege;
                                            $currentRepresentativeType = null;
                                            echo '<div class="mb-2">';
                                            echo '<div class="text-xs font-semibold text-gray-600 mb-1 uppercase">' . ucfirst($user->privilege) . '</div>';
                                        }
                                        
                                        if ($user->privilege === 'user' && $currentRepresentativeType !== $user->representative_type) {
                                            if ($currentRepresentativeType !== null) {
                                                echo '</div>';
                                            }
                                            $currentRepresentativeType = $user->representative_type;
                                            echo '<div class="ml-2 mb-1">';
                                            echo '<div class="text-xs text-gray-500 mb-1">' . ($user->representative_type ? ucfirst(str_replace('_', ' ', $user->representative_type)) : 'No Type') . '</div>';
                                        }
                                    @endphp
                                    <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer user-item {{ $user->privilege === 'user' ? 'ml-4' : '' }}">
                                        <input 
                                            type="checkbox" 
                                            name="allowed_users[]" 
                                            value="{{ $user->id }}"
                                            class="user-checkbox h-4 w-4 text-[#055498] border-gray-300 rounded focus:ring-[#055498] flex-shrink-0"
                                            {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}
                                        >
                                        <img src="{{ $profilePic }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-10 h-10 rounded-full object-cover border-2 flex-shrink-0" style="border-color: #055498;">
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-gray-700 block truncate">
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </span>
                                            <span class="text-xs text-gray-500 block truncate">{{ $user->email }}</span>
                                        </div>
                                        @if($user->privilege === 'consec')
                                            <span class="ml-auto px-2 py-0.5 text-xs rounded font-medium flex-shrink-0" style="background-color: #055498; color: #ffffff;">CONSEC</span>
                                        @endif
                                    </label>
                                @endforeach
                                @if($currentPrivilege !== null)
                                    @if($currentRepresentativeType !== null)
                                        </div>
                                    @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('allowed_users')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize CKEditor with simplified toolbar
    CKEDITOR.replace('description', {
        height: 400,
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
            { name: 'styles', items: ['Format', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Source', 'Maximize'] }
        ],
        filebrowserBrowseUrl: '{{ route("admin.media-library.browse") }}',
        filebrowserImageBrowseUrl: '{{ route("admin.media-library.browse", ["type" => "Images"]) }}',
        removePlugins: 'elementspath',
        resize_enabled: false,
        format_tags: 'p;h1;h2;h3;h4;h5;h6;pre;address;div'
    });

    // Hide CKEditor security warning notification
    CKEDITOR.on('instanceReady', function() {
        // Hide notification area immediately
        var notificationArea = document.getElementById('cke_notifications_area_description');
        if (notificationArea) {
            notificationArea.style.display = 'none';
            notificationArea.style.visibility = 'hidden';
        }
        
        // Also hide any notification areas that might appear later
        setInterval(function() {
            var notifications = document.querySelectorAll('.cke_notifications_area, #cke_notifications_area_description');
            notifications.forEach(function(notif) {
                notif.style.display = 'none';
                notif.style.visibility = 'hidden';
            });
        }, 100);
    });

    flatpickr("#scheduled_at", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
    });

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
        const isChecked = $(this).prop('checked');
        $('.user-item:visible .user-checkbox').prop('checked', isChecked);
    });

    // Update select all state when individual checkboxes change
    $(document).on('change', '.user-checkbox', function() {
        updateSelectAllState();
    });

    // Initialize select all state after DOM is ready
    $(document).ready(function() {
        updateSelectAllState();
    });

    // Banner image upload - Click handler (only when preview is hidden)
    const bannerUploadArea = document.getElementById('bannerUploadArea');
    const bannerPreviewContainer = document.getElementById('bannerPreviewContainer');
    
    if (bannerUploadArea && bannerPreviewContainer) {
        bannerUploadArea.addEventListener('click', function(e) {
            // Only trigger file picker if preview is hidden
            if (bannerPreviewContainer.classList.contains('hidden')) {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('banner_image').click();
            }
        });
    }

    // Banner image upload - Drag and drop
    const bannerDropZone = document.getElementById('bannerDropZone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        bannerDropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        bannerDropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        bannerDropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        bannerDropZone.classList.add('border-[#055498]', 'bg-blue-50');
    }

    function unhighlight(e) {
        bannerDropZone.classList.remove('border-[#055498]', 'bg-blue-50');
    }

    bannerDropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload a valid image file (JPG, PNG, GIF, WEBP).',
                });
                return;
            }
            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size must be less than 10MB.',
                });
                return;
            }
            // Set file to input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('banner_image').files = dataTransfer.files;
            previewBanner(document.getElementById('banner_image'));
        }
    }

    function previewBanner(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size must be less than 10MB.',
                });
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const bannerPreview = document.getElementById('bannerPreview');
                const bannerUploadArea = document.getElementById('bannerUploadArea');
                const bannerPreviewContainer = document.getElementById('bannerPreviewContainer');
                
                if (bannerPreview && bannerUploadArea && bannerPreviewContainer) {
                    bannerPreview.src = e.target.result;
                    bannerUploadArea.classList.add('hidden');
                    bannerPreviewContainer.classList.remove('hidden');
                    
                    // Disable pointer events on upload area to prevent reopening file picker
                    bannerUploadArea.style.pointerEvents = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    function removeBanner() {
        const bannerImage = document.getElementById('banner_image');
        const bannerUploadArea = document.getElementById('bannerUploadArea');
        const bannerPreviewContainer = document.getElementById('bannerPreviewContainer');
        
        if (bannerImage && bannerUploadArea && bannerPreviewContainer) {
            bannerImage.value = '';
            bannerUploadArea.classList.remove('hidden');
            bannerPreviewContainer.classList.add('hidden');
            
            // Re-enable click handler on upload area
            bannerUploadArea.style.pointerEvents = 'auto';
        }
    }

    // Form validation and submission
    $('#editAnnouncementForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default submission first
        
        try {
            // Update textarea with CKEditor content
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances) {
                for (var instance in CKEDITOR.instances) {
                    if (CKEDITOR.instances[instance]) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                }
            }

            // Validate title
            const title = $('#title').val().trim();
            if (!title) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter a title for the announcement.',
                });
                return false;
            }

            // Validate description
            let description = '';
            try {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.description) {
                    description = CKEDITOR.instances.description.getData().trim();
                } else {
                    // Fallback to textarea value if CKEditor not available
                    description = $('#description').val().trim();
                }
            } catch (err) {
                console.error('Error getting CKEditor content:', err);
                description = $('#description').val().trim();
            }
            
            if (!description) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter a description for the announcement.',
                });
                return false;
            }

            // Validate allowed users
            const allowedUsers = $('input[name="allowed_users[]"]:checked').length;
            if (allowedUsers === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select at least one allowed user.',
                });
                return false;
            }

            // Show loading state
            const submitBtn = $('button[type="submit"]');
            const form = $(this);
            
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            
            // Remove the event listener temporarily to allow native form submission
            form.off('submit');
            
            // Submit the form using native HTML form submission
            form[0].submit();
        } catch (error) {
            console.error('Form submission error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while submitting the form. Please try again.',
            });
            return false;
        }
    });
</script>
@endpush

