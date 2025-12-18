@extends('admin.layout')

@section('title', 'Media Library')

@php
    $pageTitle = 'Media Library';
    $headerActions = [];
    $hideDefaultActions = false;
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .media-item {
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .media-item:hover {
        transform: scale(1.05);
        z-index: 10;
    }
    .media-item.selected {
        border: 3px solid #055498;
        box-shadow: 0 0 0 3px rgba(5, 84, 152, 0.2);
    }
    .media-thumbnail {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }
    .media-icon {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 8px;
        font-size: 4rem;
        color: #6b7280;
    }
    .file-upload-area {
        border: 2px dashed #cbd5e0;
        transition: all 0.3s ease;
    }
    .file-upload-area.dragover {
        border-color: #055498;
        background-color: rgba(5, 84, 152, 0.1);
    }
    .view-toggle {
        display: flex;
        gap: 0.5rem;
    }
    .view-toggle button.active {
        background-color: #055498;
        color: white;
    }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .media-list-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .media-list-item:hover {
        background-color: #f9fafb;
    }
    .media-list-item.selected {
        background-color: rgba(5, 84, 152, 0.1);
        border-color: #055498;
    }
    #mediaModal {
        backdrop-filter: blur(2px);
    }
    #mediaModal > div {
        animation: modalSlideIn 0.3s ease-out;
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <!-- Page Title -->
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Media Library</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your media files</p>
    </div>

    <!-- Upload Area -->
    @can('upload media')
    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0">
        <button 
            onclick="toggleUploadArea()" 
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
        >
            <i class="fas fa-upload mr-2"></i>Show Upload Area
        </button>
        <button 
            onclick="document.getElementById('fileInput').click()" 
            class="px-6 py-3 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg"
            style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);"
        >
            <i class="fas fa-plus mr-2"></i>Add New Media
        </button>
        <input type="file" id="fileInput" multiple accept="*/*" class="hidden">
    </div>
    @endcan

    <!-- Upload Progress Bar (Hidden by default) -->
    <div id="uploadProgressContainer" class="mb-4 sm:mb-6 hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Uploading files...</span>
                <span id="uploadProgressPercent" class="text-sm font-semibold text-[#055498]">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="uploadProgressBar" class="h-2.5 rounded-full transition-all duration-300" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%); width: 0%;"></div>
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                <span id="uploadProgressText">Preparing upload...</span>
                <span id="uploadProgressSize">0 MB / 0 MB</span>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Upload Area (Collapsible) -->
    @can('upload media')
    <div id="uploadArea" class="file-upload-area bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-8 mb-4 sm:mb-6 text-center hidden">
        <div class="space-y-4">
            <i class="fas fa-cloud-upload-alt text-4xl" style="color: #055498;"></i>
            <div>
                <p class="text-lg font-semibold text-gray-800">Drop files to upload</p>
                <p class="text-sm text-gray-600 mt-1">or <button type="button" onclick="document.getElementById('fileInput').click()" class="text-[#055498] hover:underline font-medium">browse</button></p>
            </div>
            <p class="text-xs text-gray-500">Maximum file size: 30MB per file</p>
        </div>
    </div>
    @endcan

    <!-- Toolbar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Search and Filters -->
            <div class="flex flex-1 items-center gap-4">
                <div class="flex-1 max-w-md">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Search media files..." 
                        value="{{ request('search') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                    >
                </div>
                <select 
                    id="typeFilter" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                >
                    <option value="">All Types</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Documents</option>
                    <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
                    <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                </select>
            </div>

            <!-- View Toggle and Bulk Actions -->
            <div class="flex items-center gap-4">
                <div class="view-toggle">
                    <button 
                        id="gridViewBtn" 
                        class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 active"
                        onclick="switchView('grid')"
                        title="Grid View"
                    >
                        <i class="fas fa-th"></i>
                    </button>
                    <button 
                        id="listViewBtn" 
                        class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                        onclick="switchView('list')"
                        title="List View"
                    >
                        <i class="fas fa-list"></i>
                    </button>
                </div>
                @can('delete media library')
                <div id="bulkActions" class="hidden">
                    <button 
                        onclick="bulkDelete()" 
                        class="px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors"
                    >
                        <i class="fas fa-trash mr-2"></i>Delete Selected
                    </button>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Media Grid/List -->
    <div id="mediaContainer" class="media-grid">
        @forelse($mediaFiles as $media)
        @php
            $fileSize = \Illuminate\Support\Facades\Storage::disk('public')->exists($media->file_path) 
                ? \Illuminate\Support\Facades\Storage::disk('public')->size($media->file_path) 
                : 0;
            $fileSizeFormatted = $fileSize > 0 
                ? (round($fileSize / (1024 * 1024), 2) . ' MB') 
                : '0 MB';
            $isImage = strpos($media->file_type, 'image/') === 0;
            $fileIcon = $media->file_type === 'application/pdf' ? 'fa-file-pdf' : 
                       (strpos($media->file_type, 'video/') === 0 ? 'fa-file-video' : 
                       (strpos($media->file_type, 'audio/') === 0 ? 'fa-file-audio' : 'fa-file'));
        @endphp
        <div class="media-item bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" data-id="{{ $media->id }}" data-type="{{ $media->file_type }}" data-view="grid">
            <div class="relative">
                @can('delete media library')
                <input type="checkbox" class="media-checkbox absolute top-2 left-2 z-10 w-5 h-5" value="{{ $media->id }}" onchange="updateBulkActions()">
                @endcan
                @if($isImage)
                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->file_name }}" class="media-thumbnail" onclick="viewMedia({{ $media->id }})">
                @else
                    <div class="media-icon" onclick="viewMedia({{ $media->id }})">
                        <i class="fas {{ $fileIcon }}"></i>
                    </div>
                @endif
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 text-xs truncate">
                    {{ $media->file_name }}
                </div>
            </div>
            <!-- List view content (hidden by default) -->
            <div class="media-list-content hidden p-3">
                <div class="flex items-center space-x-4">
                    @can('delete media library')
                    <input type="checkbox" class="media-checkbox w-5 h-5" value="{{ $media->id }}" onchange="updateBulkActions()">
                    @endcan
                    @if($isImage)
                        <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->file_name }}" class="w-16 h-16 object-cover rounded">
                    @else
                        <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded">
                            <i class="fas {{ $fileIcon }} text-2xl text-gray-500"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $media->file_name }}</p>
                        <p class="text-sm text-gray-500">{{ $fileSizeFormatted }} • {{ $media->file_type }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-500">
                        <p>{{ $media->created_at->format('M d, Y') }}</p>
                        <p>{{ $media->uploader ? $media->uploader->first_name . ' ' . $media->uploader->last_name : 'Unknown' }}</p>
                    </div>
                    <button onclick="viewMedia({{ $media->id }})" class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded hover:bg-blue-200">
                        View
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
            <p class="text-lg font-medium text-gray-600">No media files found</p>
            <p class="text-sm text-gray-500 mt-2">Upload your first file to get started</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($mediaFiles->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $mediaFiles->links() }}
    </div>
    @endif
</div>

<!-- Media Preview Modal (WordPress Style) -->
<div id="mediaModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-1 lg:p-2">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-[98vw] lg:max-w-[96vw] xl:max-w-[95vw] 2xl:max-w-[92vw] max-h-[98vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-4 lg:p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-xl lg:text-2xl font-semibold text-gray-800">Attachment Details</h3>
            <button onclick="closeMediaModal()" class="text-gray-500 hover:text-gray-700 p-2">
                <i class="fas fa-times text-xl lg:text-2xl"></i>
            </button>
        </div>

        <!-- Modal Body (Two Column Layout) -->
        <div class="flex-1 overflow-auto p-4 lg:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <!-- Left Column: Media Preview -->
                <div class="space-y-4">
                    <div id="modalMediaPreview" class="bg-gray-100 rounded-lg p-0 flex items-center justify-center min-h-[300px] lg:min-h-[500px] xl:min-h-[600px] overflow-hidden">
                        <!-- Media preview will be loaded here -->
                    </div>
                </div>

                <!-- Right Column: File Information and Editable Fields -->
                <div class="space-y-6">
                    <!-- File Information -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-800 mb-3">File Information</h4>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-gray-600">Uploaded on:</span>
                                <span class="text-gray-900 ml-2" id="modalUploadedAt">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Uploaded by:</span>
                                <a href="#" id="modalUploadedBy" class="text-[#055498] hover:underline ml-2">-</a>
                            </div>
                            <div>
                                <span class="text-gray-600">File name:</span>
                                <span class="text-gray-900 ml-2" id="modalFileName">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">File type:</span>
                                <span class="text-gray-900 ml-2" id="modalFileType">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">File size:</span>
                                <span class="text-gray-900 ml-2" id="modalFileSize">-</span>
                            </div>
                            <div id="modalDimensions" class="hidden">
                                <span class="text-gray-600">Dimensions:</span>
                                <span class="text-gray-900 ml-2" id="modalDimensionsValue">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Fields -->
                    <form id="mediaDetailsForm" class="space-y-4">
                        <div>
                            <label for="modalAltText" class="block text-sm font-medium text-gray-700 mb-1">Alternative Text</label>
                            <textarea 
                                id="modalAltText" 
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                                placeholder="Learn how to describe the purpose of the image. Leave empty if the image is purely decorative."
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">Used for accessibility and SEO purposes.</p>
                        </div>

                        <div>
                            <label for="modalTitle" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input 
                                type="text" 
                                id="modalTitle" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                            >
                        </div>

                        <div>
                            <label for="modalCaption" class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                            <textarea 
                                id="modalCaption" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                            ></textarea>
                        </div>

                        <div>
                            <label for="modalDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea 
                                id="modalDescription" 
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none text-sm"
                            ></textarea>
                        </div>

                        <div>
                            <label for="modalFileUrl" class="block text-sm font-medium text-gray-700 mb-1">File URL</label>
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    id="modalFileUrl" 
                                    readonly
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                >
                                <button 
                                    type="button"
                                    onclick="copyFileUrl()" 
                                    class="px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors text-sm"
                                >
                                    <i class="fas fa-copy mr-1"></i>Copy URL
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 p-4 bg-gray-50 flex justify-between items-center">
            <div class="flex gap-4 text-sm">
                <a href="#" id="modalViewLink" target="_blank" class="text-[#055498] hover:underline">View attachment page</a>
                <a href="#" id="modalDownloadLink" download class="text-[#055498] hover:underline">Download file</a>
                @can('delete media library')
                <button onclick="deleteMedia(currentMediaId)" class="text-red-600 hover:underline">Delete permanently</button>
                @endcan
            </div>
            <div class="flex gap-2">
                <button onclick="closeMediaModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <button onclick="saveMediaDetails()" id="saveMediaBtn" class="px-4 py-2 bg-[#055498] text-white rounded-lg hover:bg-[#123a60] transition-colors">
                    <span id="saveMediaBtnText">Update</span>
                    <span id="saveMediaBtnLoader" class="hidden">Saving...</span>
                </button>
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
    // Set up axios defaults
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    let currentView = 'grid';
    let currentMediaId = null;
    let selectedMedia = new Set();

    // Toggle upload area
    function toggleUploadArea() {
        const uploadArea = document.getElementById('uploadArea');
        const btn = event.target.closest('button');
        if (uploadArea.classList.contains('hidden')) {
            uploadArea.classList.remove('hidden');
            btn.innerHTML = '<i class="fas fa-times mr-2"></i>Hide Upload Area';
        } else {
            uploadArea.classList.add('hidden');
            btn.innerHTML = '<i class="fas fa-upload mr-2"></i>Show Upload Area';
        }
    }

    // File upload handling
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');

    fileInput.addEventListener('change', handleFiles);
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFiles({ target: fileInput });
        }
    });

    async function handleFiles(e) {
        const files = e.target.files;
        if (files.length === 0) return;

        const formData = new FormData();
        let totalSize = 0;
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
            totalSize += files[i].size;
        }

        // Show progress bar
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressPercent = document.getElementById('uploadProgressPercent');
        const progressText = document.getElementById('uploadProgressText');
        const progressSize = document.getElementById('uploadProgressSize');
        
        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';
        progressText.textContent = `Uploading ${files.length} file(s)...`;
        progressSize.textContent = `0 MB / ${(totalSize / (1024 * 1024)).toFixed(2)} MB`;

        try {
            const response = await axios.post('{{ route("admin.media-library.store") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        const loadedMB = (progressEvent.loaded / (1024 * 1024)).toFixed(2);
                        const totalMB = (progressEvent.total / (1024 * 1024)).toFixed(2);
                        
                        progressBar.style.width = percentCompleted + '%';
                        progressPercent.textContent = percentCompleted + '%';
                        progressSize.textContent = `${loadedMB} MB / ${totalMB} MB`;
                        
                        if (percentCompleted === 100) {
                            progressText.textContent = 'Processing files...';
                        }
                    }
                }
            });

            // Hide progress bar
            progressContainer.classList.add('hidden');
            
            if (response.data.success) {
                progressBar.style.width = '100%';
                progressPercent.textContent = '100%';
                progressText.textContent = 'Upload complete!';
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                let errorMsg = response.data.message || 'Some files failed to upload.';
                if (response.data.errors && response.data.errors.length > 0) {
                    errorMsg += '\n\nErrors:\n' + response.data.errors.map(e => `• ${e.file}: ${e.error}`).join('\n');
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Upload Complete',
                    html: errorMsg.replace(/\n/g, '<br>'),
                    width: '500px'
                });
            }
        } catch (error) {
            // Hide progress bar on error
            progressContainer.classList.add('hidden');
            
            let errorMessage = 'An error occurred while uploading files.';
            let currentLimits = null;
            
            if (error.response) {
                // Handle validation errors (422)
                if (error.response.status === 422) {
                    const errors = error.response.data?.errors || {};
                    let validationErrors = [];
                    
                    // Collect all validation errors
                    Object.keys(errors).forEach(key => {
                        if (Array.isArray(errors[key])) {
                            errors[key].forEach(err => validationErrors.push(err));
                        } else {
                            validationErrors.push(errors[key]);
                        }
                    });
                    
                    if (validationErrors.length > 0) {
                        errorMessage = validationErrors.join('\n');
                    } else {
                        errorMessage = error.response.data?.message || 'Validation failed.';
                    }
                }
                // Handle file too large errors (413)
                else if (error.response.status === 413 || error.response.data?.message?.includes('too large')) {
                    currentLimits = error.response.data?.current_limits;
                    let limitsText = '';
                    if (currentLimits) {
                        limitsText = '\n\nCurrent PHP Limits:\n' +
                            '• upload_max_filesize: ' + currentLimits.upload_max_filesize + '\n' +
                            '• post_max_size: ' + currentLimits.post_max_size;
                    }
                    
                    errorMessage = error.response.data?.message || 'File upload failed: The file is too large.' + limitsText +
                        '\n\nTo fix:\n' +
                        '1. Check which php.ini your server uses: Visit /phpinfo\n' +
                        '2. Edit that php.ini file\n' +
                        '3. Set upload_max_filesize = 30M\n' +
                        '4. Set post_max_size = 30M\n' +
                        '5. Restart your PHP server';
                } else {
                    errorMessage = error.response.data?.message || errorMessage;
                }
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: errorMessage,
                width: '700px',
                html: errorMessage.replace(/\n/g, '<br>')
            });
        } finally {
            fileInput.value = '';
        }
    }

    // View switching
    function switchView(view) {
        currentView = view;
        const container = document.getElementById('mediaContainer');
        const items = container.querySelectorAll('.media-item');
        
        if (view === 'grid') {
            container.className = 'media-grid';
            document.getElementById('gridViewBtn').classList.add('active');
            document.getElementById('listViewBtn').classList.remove('active');
            
            items.forEach(item => {
                item.setAttribute('data-view', 'grid');
                item.querySelector('.relative').classList.remove('hidden');
                item.querySelector('.media-list-content').classList.add('hidden');
            });
        } else {
            container.className = 'space-y-2';
            document.getElementById('listViewBtn').classList.add('active');
            document.getElementById('gridViewBtn').classList.remove('active');
            
            items.forEach(item => {
                item.setAttribute('data-view', 'list');
                item.querySelector('.relative').classList.add('hidden');
                item.querySelector('.media-list-content').classList.remove('hidden');
            });
        }
    }

    // View media details
    async function viewMedia(id) {
        currentMediaId = id;
        const modal = document.getElementById('mediaModal');
        const modalMediaPreview = document.getElementById('modalMediaPreview');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modalMediaPreview.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i></div>';

        try {
            const response = await axios.get(`/admin/media-library/${id}`);
            
            if (response.data.success) {
                const media = response.data.media;

                // Display media preview (left column)
                if (media.is_image) {
                    modalMediaPreview.innerHTML = `<img src="${media.url}" alt="${media.alt_text || media.file_name}" class="max-w-full h-auto rounded-lg">`;
                } else if (media.file_type === 'application/pdf') {
                    // PDF Preview using iframe
                    // Ensure URL is absolute
                    const absoluteUrl = media.url.startsWith('http') ? media.url : (window.location.origin + (media.url.startsWith('/') ? '' : '/') + media.url);
                    
                    modalMediaPreview.innerHTML = `
                        <div class="w-full h-full flex flex-col">
                            <div class="flex items-center justify-between mb-2 p-2 bg-gray-50 rounded-t-lg border-b">
                                <span class="text-sm font-medium text-gray-700">PDF Preview</span>
                                <a href="${absoluteUrl}" target="_blank" data-pdf-modal="false" class="text-sm text-[#055498] hover:underline flex items-center gap-1" onclick="event.stopPropagation(); window.open('${absoluteUrl}', '_blank', 'noopener,noreferrer'); return false;">
                                    <i class="fas fa-external-link-alt"></i> Open in new tab
                                </a>
                            </div>
                            <iframe 
                                src="${absoluteUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                                class="w-full flex-1 rounded-b-lg border-0"
                                style="min-height: 500px;"
                                title="PDF Preview: ${media.file_name}"
                            ></iframe>
                            <p class="text-xs text-gray-500 mt-2 text-center">If the PDF doesn't load, <a href="${absoluteUrl}" target="_blank" data-pdf-modal="false" class="text-[#055498] hover:underline" onclick="event.stopPropagation(); window.open('${absoluteUrl}', '_blank', 'noopener,noreferrer'); return false;">click here to open it</a></p>
                        </div>
                    `;
                } else if (media.file_type.startsWith('video/')) {
                    modalMediaPreview.innerHTML = `<video src="${media.url}" controls class="max-w-full rounded-lg"></video>`;
                } else if (media.file_type.startsWith('audio/')) {
                    modalMediaPreview.innerHTML = `<audio src="${media.url}" controls class="w-full"></audio>`;
                } else {
                    modalMediaPreview.innerHTML = `<div class="text-center py-8"><i class="fas fa-file text-6xl text-gray-400"></i><p class="mt-4 text-gray-600">Preview not available for this file type</p></div>`;
                }

                // Populate file information (right column)
                document.getElementById('modalUploadedAt').textContent = media.uploaded_at_short;
                const uploadedByLink = document.getElementById('modalUploadedBy');
                uploadedByLink.textContent = media.uploaded_by;
                if (media.uploaded_by_id) {
                    uploadedByLink.href = `/profile/view/${media.uploaded_by_id}`;
                } else {
                    uploadedByLink.href = '#';
                    uploadedByLink.onclick = (e) => e.preventDefault();
                }
                document.getElementById('modalFileName').textContent = media.file_name;
                document.getElementById('modalFileType').textContent = media.file_type;
                document.getElementById('modalFileSize').textContent = media.size_formatted;
                
                // Show dimensions if available
                if (media.dimensions) {
                    document.getElementById('modalDimensions').classList.remove('hidden');
                    document.getElementById('modalDimensionsValue').textContent = media.dimensions;
                } else {
                    document.getElementById('modalDimensions').classList.add('hidden');
                }

                // Populate editable fields
                document.getElementById('modalTitle').value = media.title || '';
                document.getElementById('modalAltText').value = media.alt_text || '';
                document.getElementById('modalCaption').value = media.caption || '';
                document.getElementById('modalDescription').value = media.description || '';
                document.getElementById('modalFileUrl').value = media.url;

                // Set up links
                document.getElementById('modalViewLink').href = media.url;
                // Use custom download route so filename uses title instead of UUID
                document.getElementById('modalDownloadLink').href = `/admin/media-library/${media.id}/download`;
            }
        } catch (error) {
            modalMediaPreview.innerHTML = '<div class="text-center py-8 text-red-600">Error loading media details</div>';
        }
    }

    // Save media details
    async function saveMediaDetails() {
        if (!currentMediaId) return;

        const saveBtn = document.getElementById('saveMediaBtn');
        const saveBtnText = document.getElementById('saveMediaBtnText');
        const saveBtnLoader = document.getElementById('saveMediaBtnLoader');

        saveBtn.disabled = true;
        saveBtnText.classList.add('hidden');
        saveBtnLoader.classList.remove('hidden');

        try {
            const response = await axios.post(`/admin/media-library/${currentMediaId}/update`, {
                title: document.getElementById('modalTitle').value,
                alt_text: document.getElementById('modalAltText').value,
                caption: document.getElementById('modalCaption').value,
                description: document.getElementById('modalDescription').value,
            });

            if (response.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'Failed to update media details.'
            });
        } finally {
            saveBtn.disabled = false;
            saveBtnText.classList.remove('hidden');
            saveBtnLoader.classList.add('hidden');
        }
    }

    // Copy file URL to clipboard
    function copyFileUrl() {
        const urlInput = document.getElementById('modalFileUrl');
        urlInput.select();
        urlInput.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'File URL copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        } catch (err) {
            // Fallback for modern browsers
            navigator.clipboard.writeText(urlInput.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'File URL copied to clipboard',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
    }

    function closeMediaModal() {
        const modal = document.getElementById('mediaModal');
        const preview = document.getElementById('modalMediaPreview');

        // Stop any playing media (video / audio / iframe)
        if (preview) {
            const mediaElements = preview.querySelectorAll('video, audio, iframe');
            mediaElements.forEach(el => {
                try {
                    const tag = el.tagName.toLowerCase();
                    if (tag === 'video' || tag === 'audio') {
                        // Pause and reset
                        el.pause();
                        el.currentTime = 0;
                        // Clear source to fully stop download/stream
                        el.removeAttribute('src');
                        if (typeof el.load === 'function') {
                            el.load();
                        }
                    } else if (tag === 'iframe') {
                        // Clear PDF / other iframe content
                        el.src = '';
                    }
                } catch (e) {
                    // Fail silently; just ensure modal closes
                }
            });

            // Clear preview content
            preview.innerHTML = '';
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentMediaId = null;
    }

    // Delete media
    async function deleteMedia(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.delete(`/admin/media-library/${id}`);
                    
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            closeMediaModal();
                            window.location.reload();
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to delete media file.'
                    });
                }
            }
        });
    }

    // Bulk actions
    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.media-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        
        if (checkboxes.length > 0) {
            bulkActions.classList.remove('hidden');
            selectedMedia.clear();
            checkboxes.forEach(cb => selectedMedia.add(cb.value));
        } else {
            bulkActions.classList.add('hidden');
            selectedMedia.clear();
        }
    }

    async function bulkDelete() {
        if (selectedMedia.size === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedMedia.size} file(s). This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete them!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await axios.post('/admin/media-library/bulk-delete', {
                        ids: Array.from(selectedMedia)
                    });
                    
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
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to delete media files.'
                    });
                }
            }
        });
    }

    // Search and filter
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const search = this.value;
            const type = document.getElementById('typeFilter').value;
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (type) params.append('type', type);
            window.location.href = '{{ route("admin.media-library.index") }}?' + params.toString();
        }
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        const search = document.getElementById('searchInput').value;
        const type = this.value;
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (type) params.append('type', type);
        window.location.href = '{{ route("admin.media-library.index") }}?' + params.toString();
    });

    // Close modal on outside click or ESC key
    document.getElementById('mediaModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMediaModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('mediaModal').classList.contains('hidden')) {
            closeMediaModal();
        }
    });
</script>
@endpush

