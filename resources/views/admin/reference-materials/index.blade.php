@extends('admin.layout')

@section('title', 'Reference Materials')

@php
    $pageTitle = 'Reference Materials';
    $hideDefaultActions = false;
    $currentSort = $sort ?? 'modified';
    $currentDir = $dir ?? 'desc';
@endphp

@push('styles')
<style>
    .drive-search-wrap { background: #f1f3f4; border-radius: 8px; padding: 0.5rem 1rem; }
    .drive-search-wrap:focus-within { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
    .drive-search-wrap input { background: transparent; border: none; }
    .drive-search-wrap input:focus { outline: none; }
    .drive-row:hover { background: #f8f9fa; }
    .drive-row td:last-child { overflow: visible; }
    .drive-table th { font-weight: 500; color: #5f6368; font-size: 0.75rem; }
    .drive-sort-btn { color: #5f6368; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; }
    .drive-sort-btn:hover { background: rgba(0,0,0,0.06); color: #202124; }
    .drive-sort-btn.active { color: #1a73e8; }
    .file-icon-wrap { width: 40px; height: 40px; border-radius: 10px; background: #e8f0fe; color: #1a73e8; display: flex; align-items: center; justify-content: center; }
    .ref-material-floating-menu { position: fixed; z-index: 999999; display: none; min-width: 180px; padding: 0.35rem 0; background: #fff; border-radius: 8px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); border: 1px solid #e5e7eb; }
    .ref-material-floating-menu a, .ref-material-floating-menu button { display: flex; align-items: center; gap: 0.5rem; width: 100%; padding: 0.5rem 0.75rem; text-left; font-size: 0.875rem; color: #202124; border: none; background: none; cursor: pointer; border-radius: 0; box-sizing: border-box; font-family: inherit; }
    .ref-material-floating-menu a:hover, .ref-material-floating-menu button:hover { background: #f1f3f4; }
    .ref-material-floating-menu .menu-item-danger { color: #dc2626; }
    .view-toggle-btn { padding: 0.5rem; border-radius: 50%; color: #5f6368; }
    .view-toggle-btn:hover { background: rgba(0,0,0,0.06); color: #202124; }
    .view-toggle-btn.active { color: #1a73e8; background: rgba(26, 115, 232, 0.08); }
    .file-card { transition: all 0.15s ease; }
    .file-card:hover { background: #f8f9fa; }
    .file-icon-grid { width: 48px; height: 48px; border-radius: 12px; background: #e8f0fe; color: #1a73e8; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
</style>
@endpush

@section('content')
<div id="refMaterialsPage" class="p-4 sm:p-6 bg-white min-h-screen">
    @if($notice ?? null)
    <div class="mb-4">
        <form action="{{ route('admin.reference-materials.index') }}" method="get" class="drive-search-wrap flex items-center gap-3 max-w-xl">
            <input type="hidden" name="notice" value="{{ $notice->id }}">
            <i class="fas fa-search text-gray-500"></i>
            <input type="search" id="materialSearch" name="q" value="{{ request()->query('q', $q ?? '') }}" placeholder="Search in this folder" class="flex-1 py-2 text-sm text-gray-900 placeholder-gray-500">
            @if(!empty(request()->query('q', $q ?? '')))
                <a href="{{ route('admin.reference-materials.index', ['notice' => $notice->id]) }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times-circle"></i></a>
            @endif
        </form>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2 pb-3 border-b border-gray-200">
        <div class="flex items-center gap-2 flex-wrap">
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('admin.reference-materials.index') }}" class="text-blue-600 hover:text-blue-800 hover:underline">Reference Materials</a>
                @if($notice ?? null)
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-medium truncate max-w-[200px] sm:max-w-md" title="{{ $notice->title }}">{{ $notice->title }}</span>
                @endif
            </nav>
            @if($notice ?? null)
                <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="ml-2 inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-plus"></i> New
                </button>
            @endif
        </div>
        @if($notice ?? null)
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" id="refDownloadAllBtn" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium border border-gray-200 disabled:opacity-70 disabled:cursor-not-allowed" data-notice-id="{{ $notice->id }}" data-download-url="{{ route('admin.reference-materials.download-all', ['notice' => $notice->id]) }}" title="Download all files as zip">
                <i class="ref-download-all-icon fas fa-file-archive"></i>
                <span class="ref-download-all-text">Download all</span>
            </button>
            @php $baseUrl = route('admin.reference-materials.index', ['notice' => $notice->id]); $queryParams = request()->only(['q']); @endphp
            <div class="flex items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50/50 overflow-hidden">
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['sort' => 'modified', 'dir' => ($currentSort === 'modified' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="drive-sort-btn {{ $currentSort === 'modified' ? 'active' : '' }}">Modified</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['sort' => 'name', 'dir' => ($currentSort === 'name' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="drive-sort-btn {{ $currentSort === 'name' ? 'active' : '' }}">Name</a>
            </div>
            <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50/50 p-0.5">
                <button type="button" onclick="setFilesView('list')" id="filesViewListBtn" class="view-toggle-btn {{ ($filesView ?? 'list') === 'list' ? 'active' : '' }}" title="List view"><i class="fas fa-list"></i></button>
                <button type="button" onclick="setFilesView('grid')" id="filesViewGridBtn" class="view-toggle-btn {{ ($filesView ?? 'list') === 'grid' ? 'active' : '' }}" title="Grid view"><i class="fas fa-th-large"></i></button>
            </div>
        </div>
        @endif
    </div>

    @if($notice ?? null)
    <div id="refDropZone" class="mb-4 flex items-center gap-2 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600" data-notice-id="{{ $notice->id }}">
        <i class="fas fa-cloud-upload-alt text-gray-400"></i>
        <span>Drag & drop files here to upload to this folder, or click <span class="font-semibold">New</span>.</span>
    </div>
    @endif

    @if(isset($noticeId) && $noticeId && ($notice ?? null))
    <div id="uploadModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Upload files to this folder</h3>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="notice_id" value="{{ $notice->id }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Brief description of the upload"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Files <span class="text-red-500">*</span></label>
                    <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Max 30MB per file. PDF, DOC, XLS, PPT, images.</p>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden'); refDroppedFiles = null;" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" id="uploadBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-70 inline-flex items-center gap-2">
                        <span class="upload-btn-text">Upload</span>
                        <span id="uploadSpinner" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div id="filesListView" class="{{ ($filesView ?? 'list') === 'grid' ? 'hidden' : '' }} bg-white rounded-lg border border-gray-200">
        @if(isset($filesPaginated) && $filesPaginated->count() > 0)
        <table class="min-w-full drive-table">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/50">
                    <th class="text-left py-3 px-4 w-12"></th>
                    <th class="text-left py-3 px-4">Name</th>
                    <th class="text-left py-3 px-4">Owner</th>
                    <th class="text-left py-3 px-4">Modified</th>
                    <th class="text-left py-3 px-4">Size</th>
                    <th class="w-12"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($filesPaginated as $file)
                @php
                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                    $icon = ($ext === 'pdf') ? 'fa-file-pdf' : (in_array($ext, ['doc','docx']) ? 'fa-file-word' : (in_array($ext, ['xls','xlsx']) ? 'fa-file-excel' : 'fa-file-alt'));
                    $fileUrl = asset('storage/' . $file->file_path);
                    $profileUrl = $file->owner_avatar ? asset('storage/' . $file->owner_avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($file->owner_name) . '&size=32&background=e8f0fe&color=1a73e8';
                    $sz = $file->file_size ?? 0;
                    $sizeFormatted = $sz >= 1048576 ? number_format($sz / 1048576, 2) . ' MB' : ($sz >= 1024 ? number_format($sz / 1024, 2) . ' KB' : ($sz > 0 ? $sz . ' B' : '—'));
                @endphp
                <tr class="drive-row border-b border-gray-100 relative">
                    <td class="py-3 px-4">
                        <div class="file-icon-wrap">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="text-gray-900 font-medium hover:underline focus:outline-none focus:underline">
                            {{ $file->file_name }}
                        </a>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $profileUrl }}" alt="" class="h-6 w-6 rounded-full object-cover">
                            <span class="text-sm text-gray-700">{{ $file->owner_name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-600">
                        {{ $file->modified_at->format('M d, Y') }}
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-600">{{ $sizeFormatted }}</td>
                    <td class="py-3 px-4 text-right">
                        <button type="button" class="ref-material-menu-btn p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none" aria-label="Actions"
                            data-file-url="{{ $fileUrl }}"
                            data-file-name="{{ e($file->file_name) }}"
                            data-material-id="{{ $file->material_id }}"
                            data-media-id="{{ $file->media_id }}">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-600">Showing {{ $filesPaginated->firstItem() ?? 0 }}–{{ $filesPaginated->lastItem() ?? 0 }} of {{ $filesPaginated->total() }}</p>
            <div class="flex gap-1">{{ $filesPaginated->links() }}</div>
        </div>
        @else
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-file-alt text-3xl"></i>
            </div>
            <p class="text-gray-600">No files in this folder</p>
            @if($notice ?? null)
                <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="mt-3 text-blue-600 hover:underline font-medium">Upload files</button>
            @endif
        </div>
        @endif
    </div>

    @if(isset($filesPaginated) && $filesPaginated->count() > 0)
    <div id="filesGridView" class="{{ ($filesView ?? 'list') === 'grid' ? '' : 'hidden' }} grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($filesPaginated as $file)
        @php
            $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
            $icon = ($ext === 'pdf') ? 'fa-file-pdf' : (in_array($ext, ['doc','docx']) ? 'fa-file-word' : (in_array($ext, ['xls','xlsx']) ? 'fa-file-excel' : 'fa-file-alt'));
            $fileUrl = asset('storage/' . $file->file_path);
            $profileUrl = $file->owner_avatar ? asset('storage/' . $file->owner_avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($file->owner_name) . '&size=32&background=e8f0fe&color=1a73e8';
            $sz = $file->file_size ?? 0;
            $sizeFormatted = $sz >= 1048576 ? number_format($sz / 1048576, 2) . ' MB' : ($sz >= 1024 ? number_format($sz / 1024, 2) . ' KB' : ($sz > 0 ? $sz . ' B' : '—'));
        @endphp
        <div class="file-card rounded-xl border border-gray-200 p-4 relative group">
            <div class="flex flex-col items-center text-center">
                <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="block mb-3">
                    <div class="file-icon-grid mx-auto">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                </a>
                <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-900 truncate w-full hover:underline" title="{{ $file->file_name }}">{{ $file->file_name }}</a>
                <p class="text-xs text-gray-500 mt-1">{{ $file->owner_name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $file->modified_at->format('M d, Y') }} · {{ $sizeFormatted }}</p>
            </div>
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button type="button" class="ref-material-menu-btn p-1.5 rounded-full text-gray-500 hover:bg-gray-100"
                    data-file-url="{{ $fileUrl }}"
                    data-file-name="{{ e($file->file_name) }}"
                    data-material-id="{{ $file->material_id }}"
                    data-media-id="{{ $file->media_id }}">
                    <i class="fas fa-ellipsis-v text-sm"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    <div id="filesGridPagination" class="{{ ($filesView ?? 'list') === 'grid' ? '' : 'hidden' }} mt-4 flex items-center justify-between">
        <p class="text-sm text-gray-600">Showing {{ $filesPaginated->firstItem() ?? 0 }}–{{ $filesPaginated->lastItem() ?? 0 }} of {{ $filesPaginated->total() }}</p>
        <div class="flex gap-1">{{ $filesPaginated->links() }}</div>
    </div>
    @endif
</div>

<script>
(function() {
    var view = localStorage.getItem('refMaterialsFilesView') || 'list';
    var listEl = document.getElementById('filesListView');
    var gridEl = document.getElementById('filesGridView');
    var gridPagEl = document.getElementById('filesGridPagination');
    var listBtn = document.getElementById('filesViewListBtn');
    var gridBtn = document.getElementById('filesViewGridBtn');
    if (!listEl) return;
    if (view === 'grid' && gridEl) {
        listEl.classList.add('hidden');
        gridEl.classList.remove('hidden');
        if (gridPagEl) gridPagEl.classList.remove('hidden');
        if (listBtn) listBtn.classList.remove('active');
        if (gridBtn) gridBtn.classList.add('active');
    } else {
        listEl.classList.remove('hidden');
        if (gridEl) gridEl.classList.add('hidden');
        if (gridPagEl) gridPagEl.classList.add('hidden');
        if (listBtn) listBtn.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
    }
})();
function setFilesView(v) {
    localStorage.setItem('refMaterialsFilesView', v);
    var listEl = document.getElementById('filesListView');
    var gridEl = document.getElementById('filesGridView');
    var gridPagEl = document.getElementById('filesGridPagination');
    var listBtn = document.getElementById('filesViewListBtn');
    var gridBtn = document.getElementById('filesViewGridBtn');
    if (v === 'grid' && gridEl) {
        listEl.classList.add('hidden');
        gridEl.classList.remove('hidden');
        if (gridPagEl) gridPagEl.classList.remove('hidden');
        listBtn.classList.remove('active');
        gridBtn.classList.add('active');
    } else {
        listEl.classList.remove('hidden');
        if (gridEl) gridEl.classList.add('hidden');
        if (gridPagEl) gridPagEl.classList.add('hidden');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    }
}
// Drag & drop upload support – upload directly on drop (no modal)
var refDroppedFiles = null;
function doRefDropUpload(files, noticeId) {
    if (!files || !files.length || !noticeId) return;
    var fd = new FormData();
    fd.append('notice_id', noticeId);
    fd.append('description', 'Uploaded via drag & drop');
    fd.append('_token', document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);
    var url = '{{ route("admin.reference-materials.store") }}';
    if (typeof Swal !== 'undefined') {
        Swal.fire({ title: 'Uploading...', text: files.length + ' file(s)', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
    }
    if (typeof axios !== 'undefined') {
        axios.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
            .then(function(response) {
                if (response.data.success) {
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Uploaded!', text: response.data.message, timer: 1500, showConfirmButton: false }).then(function() {
                        if (response.data.redirect) window.location.href = response.data.redirect; else location.reload();
                    });
                    else { if (response.data.redirect) window.location.href = response.data.redirect; else location.reload(); }
                }
            })
            .catch(function(error) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Upload failed', text: (error.response && error.response.data && error.response.data.message) ? error.response.data.message : 'Please try again.' });
                else alert('Upload failed.');
            });
    } else {
        fetch(url, { method: 'POST', body: fd }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) { if (data.redirect) window.location.href = data.redirect; else location.reload(); }
        }).catch(function() { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Upload failed' }); else alert('Upload failed.'); });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    @if($notice ?? null)
    var noticeId = document.getElementById('refDropZone') && document.getElementById('refDropZone').getAttribute('data-notice-id');
    var dropZone = document.getElementById('refDropZone') || document.getElementById('filesListView');
    if (dropZone) {
        ['dragenter', 'dragover'].forEach(function(evt) {
            dropZone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-blue-400', 'bg-blue-50/50');
            });
        });
        ['dragleave', 'dragend'].forEach(function(evt) {
            dropZone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-blue-400', 'bg-blue-50/50');
            });
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50/50');
            if (!e.dataTransfer || !e.dataTransfer.files || !e.dataTransfer.files.length) return;
            var nid = document.getElementById('refDropZone') && document.getElementById('refDropZone').getAttribute('data-notice-id');
            if (nid) doRefDropUpload(e.dataTransfer.files, nid);
        });
    }
    var contentEl = document.getElementById('refMaterialsPage');
    if (contentEl && noticeId) {
        ['dragenter', 'dragover'].forEach(function(evt) {
            contentEl.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                var dz = document.getElementById('refDropZone');
                if (dz) dz.classList.add('border-blue-400', 'bg-blue-50/50');
            });
        });
        contentEl.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var dz = document.getElementById('refDropZone');
            if (dz) dz.classList.remove('border-blue-400', 'bg-blue-50/50');
            if (!e.dataTransfer || !e.dataTransfer.files || !e.dataTransfer.files.length) return;
            var nid = document.getElementById('refDropZone') && document.getElementById('refDropZone').getAttribute('data-notice-id');
            if (nid) doRefDropUpload(e.dataTransfer.files, nid);
        });
    }
    @endif
});
function getRefFloatingMenu() {
    var el = document.getElementById('refMaterialFloatingMenu');
    if (!el) {
        el = document.createElement('div');
        el.id = 'refMaterialFloatingMenu';
        el.className = 'ref-material-floating-menu';
        document.body.appendChild(el);
        el.addEventListener('click', function(ev) {
            ev.stopPropagation();
            var pdfLink = ev.target.closest('a.ref-open-pdf');
            if (pdfLink) {
                ev.preventDefault();
                var u = pdfLink.getAttribute('data-pdf-url');
                var t = pdfLink.getAttribute('data-pdf-title') || 'PDF';
                closeRefMenu();
                if (typeof openGlobalPdfModal === 'function' && u) openGlobalPdfModal(u, t);
                else if (u) window.open(u, '_blank');
                return;
            }
            var b = ev.target.closest('button[data-action]');
            if (!b) return;
            var action = b.getAttribute('data-action');
            var mid = b.getAttribute('data-material-id');
            var vid = b.getAttribute('data-media-id');
            var fn = b.getAttribute('data-file-name') || '';
            closeRefMenu();
            if (action === 'rename') renameFile(parseInt(mid, 10), parseInt(vid, 10), fn);
            if (action === 'remove') deleteFile(parseInt(mid, 10), parseInt(vid, 10), fn);
        });
    }
    return el;
}
function escapeJsStr(s) {
    if (s == null) return '';
    return String(s).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
}
function closeRefMenu() {
    var el = document.getElementById('refMaterialFloatingMenu');
    if (el) el.style.display = 'none';
}
function openRefMenu(btn, contextEvent) {
    var url = btn.getAttribute('data-file-url') || '';
    var fileName = btn.getAttribute('data-file-name') || '';
    var materialId = btn.getAttribute('data-material-id') || '';
    var mediaId = btn.getAttribute('data-media-id') || '';
    var menu = getRefFloatingMenu();
    var isPdf = /\.pdf$/i.test(fileName || '');
    var safeUrl = url.replace(/"/g, '&quot;');
    var safeName = (fileName || '').replace(/"/g, '&quot;');
    var openMarkup = isPdf
        ? '<a href="javascript:void(0)" class="ref-open-pdf" data-pdf-url="' + safeUrl + '" data-pdf-title="' + safeName + '"><i class="fas fa-external-link-alt text-gray-500 w-4"></i> Open</a>'
        : '<a href="' + safeUrl + '" target="_blank" rel="noopener"><i class="fas fa-external-link-alt text-gray-500 w-4"></i> Open</a>';
    menu.innerHTML =
        openMarkup +
        '<a href="' + safeUrl + '" download="' + safeName + '"><i class="fas fa-download text-gray-500 w-4"></i> Download</a>' +
        '<button type="button" data-action="rename" data-material-id="' + materialId + '" data-media-id="' + mediaId + '" data-file-name="' + fileName.replace(/"/g, '&quot;') + '"><i class="fas fa-edit text-gray-500 w-4"></i> Rename</button>' +
        '<button type="button" class="menu-item-danger" data-action="remove" data-material-id="' + materialId + '" data-media-id="' + mediaId + '" data-file-name="' + fileName.replace(/"/g, '&quot;') + '"><i class="fas fa-trash w-4"></i> Remove</button>';
    menu.style.display = 'block';
    var menuW = 180;
    var menuH = 180;
    var pad = 8;
    var vw = window.innerWidth;
    var vh = window.innerHeight;
    menu.offsetWidth;
    menuW = menu.offsetWidth;
    menuH = menu.offsetHeight;
    var left, top;
    if (contextEvent) {
        left = contextEvent.clientX;
        top = contextEvent.clientY + 4;
    } else {
        var rect = btn.getBoundingClientRect();
        left = rect.right - menuW;
        top = rect.bottom + 4;
    }
    if (left < pad) left = pad;
    if (left + menuW > vw - pad) left = vw - menuW - pad;
    if (top + menuH > vh - pad) top = vh - menuH - pad;
    if (top < pad) top = pad;
    menu.style.left = left + 'px';
    menu.style.top = top + 'px';
}
document.addEventListener('click', function(e) {
    if (e.target.closest('.ref-material-floating-menu') || e.target.closest('.ref-material-menu-btn')) return;
    closeRefMenu();
});
document.addEventListener('contextmenu', function(e) {
    if (!e.target.closest('.ref-material-floating-menu')) return;
    e.preventDefault();
});
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ref-material-menu-btn').forEach(function(btn) {
        btn.addEventListener('click', function(ev) {
            ev.preventDefault();
            ev.stopPropagation();
            openRefMenu(btn);
        });
    });
    // Right-click on list row or grid card opens the same actions menu at cursor
    document.querySelectorAll('.drive-row').forEach(function(row) {
        row.addEventListener('contextmenu', function(ev) {
            var menuBtn = row.querySelector('.ref-material-menu-btn');
            if (menuBtn) {
                ev.preventDefault();
                ev.stopPropagation();
                openRefMenu(menuBtn, ev);
            }
        });
    });
    document.querySelectorAll('.file-card').forEach(function(card) {
        card.addEventListener('contextmenu', function(ev) {
            var menuBtn = card.querySelector('.ref-material-menu-btn');
            if (menuBtn) {
                ev.preventDefault();
                ev.stopPropagation();
                openRefMenu(menuBtn, ev);
            }
        });
    });
    var downloadAllBtn = document.getElementById('refDownloadAllBtn');
    if (downloadAllBtn) {
        downloadAllBtn.addEventListener('click', function() {
            var btn = this;
            var url = btn.getAttribute('data-download-url');
            if (!url || btn.disabled) return;
            var icon = btn.querySelector('.ref-download-all-icon');
            var textEl = btn.querySelector('.ref-download-all-text');
            var origHtml = icon ? icon.className : '';
            var origText = textEl ? textEl.textContent : 'Download all';
            btn.disabled = true;
            if (icon) { icon.className = 'ref-download-all-icon fas fa-spinner fa-spin'; }
            if (textEl) { textEl.textContent = 'Preparing...'; }
            fetch(url, { method: 'GET', credentials: 'same-origin' })
                .then(function(res) {
                    var ct = res.headers.get('content-type') || '';
                    if (res.ok && (ct.indexOf('application/zip') !== -1 || ct.indexOf('octet-stream') !== -1)) {
                        return res.blob().then(function(blob) {
                            var disp = res.headers.get('content-disposition');
                            var name = 'reference-materials.zip';
                            if (disp) {
                                var m = disp.match(/filename\*?=(?:UTF-8'')?["']?([^"';]+)["']?/i) || disp.match(/filename=["']?([^"';]+)["']?/i);
                                if (m && m[1]) name = m[1].trim();
                            }
                            var a = document.createElement('a');
                            a.href = URL.createObjectURL(blob);
                            a.download = name;
                            a.style.display = 'none';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(a.href);
                        });
                    }
                    return res.text().then(function() { throw new Error('No files to download.'); });
                })
                .then(function() {
                    if (textEl) textEl.textContent = 'Downloaded';
                    if (icon) icon.className = 'ref-download-all-icon fas fa-check';
                    setTimeout(function() {
                        btn.disabled = false;
                        if (icon) icon.className = origHtml || 'ref-download-all-icon fas fa-file-archive';
                        if (textEl) textEl.textContent = origText;
                    }, 1500);
                })
                .catch(function(err) {
                    btn.disabled = false;
                    if (icon) icon.className = origHtml || 'ref-download-all-icon fas fa-file-archive';
                    if (textEl) textEl.textContent = origText;
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'info', title: 'Download', text: err.message || 'No files to download.' });
                    else alert(err.message || 'Download failed.');
                });
        });
    }
});
function renameFile(materialId, mediaId, currentName) {
    if (typeof Swal === 'undefined') {
        var newName = prompt('Rename file:', currentName);
        if (newName === null || newName.trim() === '') return;
        doRenameSubmit(materialId, mediaId, newName.trim());
        return;
    }
    Swal.fire({
        title: 'Rename file',
        input: 'text',
        inputValue: currentName,
        inputPlaceholder: 'Enter file name',
        showCancelButton: true,
        confirmButtonText: 'Rename',
        cancelButtonText: 'Cancel',
        inputValidator: function(value) {
            if (!value || !value.trim()) return 'Please enter a file name';
            return null;
        }
    }).then(function(result) {
        if (result.isConfirmed && result.value) {
            doRenameSubmit(materialId, mediaId, result.value.trim());
        }
    });
}
function doRenameSubmit(materialId, mediaId, newName) {
    var token = document.querySelector('meta[name="csrf-token"]');
    var content = token ? (token.getAttribute ? token.getAttribute('content') : token.content) : '';
    fetch('{{ route("admin.reference-materials.rename-file") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': content, 'Accept': 'application/json' },
        body: JSON.stringify({ material_id: materialId, media_id: mediaId, file_name: newName })
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Renamed', text: data.message, timer: 1200, showConfirmButton: false }).then(function() { location.reload(); });
            else location.reload();
        } else {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to rename.' }); else alert(data.message || 'Failed to rename.');
        }
    }).catch(function() {
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to rename file.' }); else alert('Failed to rename file.');
    });
}
function deleteFile(materialId, mediaId, fileName) {
    var doDelete = function() {
        var token = document.querySelector('meta[name="csrf-token"]');
        var content = token ? (token.getAttribute ? token.getAttribute('content') : token.content) : '';
        fetch('{{ route("admin.reference-materials.remove-attachment") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': content, 'Accept': 'application/json' },
            body: JSON.stringify({ material_id: materialId, media_id: mediaId })
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Removed', text: data.message || 'File removed.', timer: 1200, showConfirmButton: false })
                        .then(function() { location.reload(); });
                } else {
                    location.reload();
                }
            } else {
                var msg = data.message || 'Failed to remove.';
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg }); else alert(msg);
            }
        }).catch(function() {
            var msg = 'Failed to remove file.';
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg }); else alert(msg);
        });
    };

    if (typeof Swal === 'undefined') {
        if (confirm('Remove "' + fileName + '"? This cannot be undone.')) doDelete();
        return;
    }

    var safeName = (fileName || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    Swal.fire({
        title: 'Remove file?',
        html: '<p class="text-gray-700">Remove <strong class="break-all">' + safeName + '</strong>?</p><p class="text-sm text-gray-500 mt-2">This cannot be undone.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        width: '420px'
    }).then(function(result) {
        if (result.isConfirmed) doDelete();
    });
}
</script>

<!-- Reject Modal (used on show page; kept for compatibility) -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">Reject Reference Materials</h3>
        <form id="rejectForm">
            <input type="hidden" id="rejectMaterialId" name="material_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
                <textarea id="rejectReason" name="rejection_reason" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none" placeholder="Please provide a reason for rejecting this submission..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    if (typeof axios !== 'undefined') {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
    var uploadFormEl = document.getElementById('uploadForm');
    if (uploadFormEl) {
        uploadFormEl.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var btn = document.getElementById('uploadBtn');
            var spinner = document.getElementById('uploadSpinner');
            var btnText = btn ? btn.querySelector('.upload-btn-text') : null;
            var filesInput = form.querySelector('input[name="files[]"]');
            var hasInputFiles = filesInput && filesInput.files && filesInput.files.length > 0;
            var hasDroppedFiles = refDroppedFiles && refDroppedFiles.length > 0;
            if (!hasInputFiles && !hasDroppedFiles) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Select files', text: 'Please select or drop at least one file to upload.' });
                else alert('Please select or drop at least one file to upload.');
                return;
            }
            if (btn) btn.disabled = true;
            if (btnText) btnText.textContent = 'Uploading...';
            if (spinner) spinner.classList.remove('hidden');
            // Build FormData manually to avoid duplicating files
            var fd = new FormData();
            if (hasInputFiles) {
                for (var i = 0; i < filesInput.files.length; i++) {
                    fd.append('files[]', filesInput.files[i]);
                }
            }
            if (hasDroppedFiles) {
                for (var j = 0; j < refDroppedFiles.length; j++) {
                    fd.append('files[]', refDroppedFiles[j]);
                }
                refDroppedFiles = null;
            }
            fd.append('notice_id', form.querySelector('input[name="notice_id"]').value);
            fd.append('description', (form.querySelector('textarea[name="description"]') || {}).value || '');
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            var url = '{{ route("admin.reference-materials.store") }}';
            if (typeof axios !== 'undefined') {
                axios.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                    .then(function(response) {
                        if (response.data.success) {
                            (typeof Swal !== 'undefined' ? Swal.fire({ icon: 'success', title: 'Uploaded!', text: response.data.message, timer: 1500, showConfirmButton: false }) : Promise.resolve()).then(function() {
                                if (response.data.redirect) window.location.href = response.data.redirect;
                                else location.reload();
                            });
                        }
                    })
                    .catch(function(error) {
                        if (btn) btn.disabled = false;
                        if (btnText) btnText.textContent = 'Upload';
                        if (spinner) spinner.classList.add('hidden');
                        var msg = (error.response && error.response.data && error.response.data.message) ? error.response.data.message : 'Failed to upload files. Please try again.';
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Upload failed', text: msg }); else alert(msg);
                    });
            } else {
                fetch(url, { method: 'POST', body: fd }).then(function(r) { return r.json(); }).then(function(data) {
                    if (data.success && data.redirect) window.location.href = data.redirect;
                    else location.reload();
                }).catch(function() {
                    if (btn) btn.disabled = false;
                    if (btnText) btnText.textContent = 'Upload';
                    if (spinner) spinner.classList.add('hidden');
                    alert('Upload failed. Please try again.');
                });
            }
        });
    }
</script>
@endpush
