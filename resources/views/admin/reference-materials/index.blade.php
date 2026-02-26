@extends('admin.layout')

@section('title', 'Reference Materials')

@php
    $pageTitle = 'Reference Materials';
    $hideDefaultActions = false;
    $currentSort = $sort ?? 'modified';
    $currentDir = $dir ?? 'desc';
@endphp

@php
    $fileIconStyle = function ($ext) {
        return match (true) {
            $ext === 'pdf' => 'bg-red-100 text-red-700',
            in_array($ext, ['xls', 'xlsx']) => 'bg-emerald-100 text-emerald-700',
            in_array($ext, ['doc', 'docx']) => 'bg-blue-100 text-blue-700',
            in_array($ext, ['ppt', 'pptx']) => 'bg-amber-100 text-amber-700',
            in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'bg-sky-100 text-sky-600',
            default => 'bg-gray-100 text-gray-600',
        };
    };
    $fileIconName = function ($ext) {
        return $ext === 'pdf' ? 'fa-file-pdf' : (in_array($ext, ['doc', 'docx']) ? 'fa-file-word' : (in_array($ext, ['xls', 'xlsx']) ? 'fa-file-excel' : (in_array($ext, ['ppt', 'pptx']) ? 'fa-file-powerpoint' : 'fa-file-alt')));
    };
@endphp
@push('styles')
<style>
    .ref-material-floating-menu { position: fixed; z-index: 999999; display: none; min-width: 180px; padding: 0.35rem 0; background: #fff; border-radius: 8px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); border: 1px solid #e5e7eb; }
    .ref-material-floating-menu a, .ref-material-floating-menu button { display: flex; align-items: center; gap: 0.5rem; width: 100%; padding: 0.5rem 0.75rem; text-left; font-size: 0.875rem; color: #202124; border: none; background: none; cursor: pointer; border-radius: 0; box-sizing: border-box; font-family: inherit; }
    .ref-material-floating-menu a:hover, .ref-material-floating-menu button:hover { background: #f1f3f4; }
    .ref-material-floating-menu .menu-item-danger { color: #dc2626; }
    /* Owner avatar tooltip (same as /admin/notices) */
    .ref-owner-avatar { position: relative; display: inline-block; }
    .ref-owner-avatar .ref-avatar-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background-color: #1f2937;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        z-index: 20;
        margin-bottom: 8px;
    }
    .ref-owner-avatar .ref-avatar-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #1f2937;
    }
    .ref-owner-avatar:hover .ref-avatar-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>
@endpush

@section('content')
<div id="refMaterialsPage" class="p-4 sm:p-6 bg-white min-h-screen">
    {{-- Single header row: breadcrumb + search + actions --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-3 pb-3 border-b border-gray-200">
        <div class="flex flex-wrap items-center gap-3 min-w-0">
            <nav class="flex items-center gap-2 text-sm text-gray-600 shrink-0">
                <a href="{{ route('admin.reference-materials.index') }}" class="text-[#055498] hover:underline">Reference Materials</a>
                @if($notice ?? null)
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-medium truncate max-w-[180px] sm:max-w-sm" title="{{ $notice->title }}">{{ $notice->title }}</span>
                @endif
            </nav>
            @if($notice ?? null)
            <form action="{{ route('admin.reference-materials.index') }}" method="get" class="flex items-center gap-2 flex-1 min-w-0 max-w-md rounded-xl border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:ring-2 focus-within:ring-[#055498]/20 focus-within:border-[#055498]">
                <input type="hidden" name="notice" value="{{ $notice->id }}">
                <i class="fas fa-search text-gray-400 shrink-0"></i>
                <input type="search" id="materialSearch" name="q" value="{{ request()->query('q', $q ?? '') }}" placeholder="Search in this folder" class="flex-1 min-w-0 py-1 text-sm text-gray-900 placeholder-gray-500 bg-transparent border-0 focus:outline-none focus:ring-0">
                @if(!empty(request()->query('q', $q ?? '')))
                    <a href="{{ route('admin.reference-materials.index', ['notice' => $notice->id]) }}" class="text-gray-400 hover:text-gray-600 shrink-0" title="Clear search"><i class="fas fa-times-circle"></i></a>
                @endif
            </form>
            <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-white shrink-0" style="background-color: #055498;">
                <i class="fas fa-plus"></i> New
            </button>
            @endif
        </div>
        @if($notice ?? null)
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <button type="button" id="refDownloadAllBtn" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium border border-gray-200 disabled:opacity-70 disabled:cursor-not-allowed" data-notice-id="{{ $notice->id }}" data-download-url="{{ route('admin.reference-materials.download-all', ['notice' => $notice->id]) }}" title="Download all files as zip">
                <i class="ref-download-all-icon fas fa-file-archive"></i>
                <span class="ref-download-all-text">Download all</span>
            </button>
            @php $baseUrl = route('admin.reference-materials.index', ['notice' => $notice->id]); $queryParams = request()->only(['q']); @endphp
            <div class="flex items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50/50 overflow-hidden">
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['sort' => 'modified', 'dir' => ($currentSort === 'modified' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="px-2.5 py-1 text-xs rounded {{ $currentSort === 'modified' ? 'bg-[#055498] text-white font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Modified</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['sort' => 'name', 'dir' => ($currentSort === 'name' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="px-2.5 py-1 text-xs rounded {{ $currentSort === 'name' ? 'bg-[#055498] text-white font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Name</a>
            </div>
            <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50/80 p-0.5">
                <button type="button" onclick="setFilesView('list')" id="filesViewListBtn" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 {{ ($filesView ?? 'list') === 'list' ? 'bg-[#055498] text-white hover:bg-[#044a85]' : '' }}" title="List view"><i class="fas fa-list"></i></button>
                <button type="button" onclick="setFilesView('grid')" id="filesViewGridBtn" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 {{ ($filesView ?? 'list') === 'grid' ? 'bg-[#055498] text-white hover:bg-[#044a85]' : '' }}" title="Grid view"><i class="fas fa-th-large"></i></button>
            </div>
        </div>
        @endif
    </div>

    @if($notice ?? null)
    <div id="refDropZone" class="mb-4 flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50/80 px-6 py-8 text-center transition-colors" data-notice-id="{{ $notice->id }}">
        <span class="text-3xl text-gray-400" aria-hidden="true">☁️</span>
        <p class="text-sm font-medium text-gray-600">Drag & drop files here</p>
        <p class="text-xs text-gray-500">or click <span class="font-semibold text-gray-700">New</span> to upload</p>
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

    <div id="filesListView" class="{{ ($filesView ?? 'list') === 'grid' ? 'hidden' : '' }} bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if(isset($filesPaginated) && $filesPaginated->count() > 0)
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/80">
                    <th class="text-left py-2 px-4 w-12 text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left py-2 px-4 w-10 text-xs font-semibold text-gray-500 uppercase tracking-wider">Owner</th>
                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Modified</th>
                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="w-32 py-2 px-4 text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($filesPaginated as $file)
                @php
                    $ext = $file->file_extension ?? strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                    $icon = $fileIconName($ext);
                    $iconClass = $fileIconStyle($ext);
                    $fileUrl = asset('storage/' . $file->file_path);
                    $profileUrl = $file->owner_avatar ? asset('storage/' . $file->owner_avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($file->owner_name) . '&size=32&background=e8f0fe&color=1a73e8';
                    $sz = $file->file_size ?? 0;
                    $sizeFormatted = $sz >= 1048576 ? number_format($sz / 1048576, 2) . ' MB' : ($sz >= 1024 ? number_format($sz / 1024, 2) . ' KB' : ($sz > 0 ? $sz . ' B' : '—'));
                    $displayName = $file->display_name ?? $file->file_name;
                    $displayTruncated = Str::length($displayName) > 42 ? Str::limit($displayName, 42) : $displayName;
                @endphp
                <tr class="drive-row group border-b border-gray-100 hover:bg-[#f0f7ff] transition-colors">
                    <td class="py-2 px-4">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $iconClass }}">
                            <i class="fas {{ $icon }} text-sm"></i>
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="text-gray-900 font-medium hover:underline focus:outline-none focus:underline truncate block max-w-[240px] sm:max-w-md" title="{{ $file->file_name }}">
                            {{ $displayTruncated }}
                        </a>
                    </td>
                    <td class="py-2 px-4">
                        @if(!empty($file->source_label ?? null))
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded" title="{{ $file->source_label }}">{{ $file->source_label }}</span>
                        @else
                            <div class="ref-owner-avatar">
                                <img src="{{ $profileUrl }}" alt="{{ $file->owner_name }}" class="h-7 w-7 rounded-full object-cover inline-block cursor-pointer">
                                <div class="ref-avatar-tooltip">{{ $file->owner_name }}</div>
                            </div>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-600">{{ $file->modified_at->format('M d, Y') }}</td>
                    <td class="py-2 px-4 text-sm text-gray-600">{{ $sizeFormatted }}</td>
                    <td class="py-2 px-4 text-right overflow-visible">
                        <div class="flex items-center justify-end gap-0.5">
                            <a href="{{ $fileUrl }}" download="{{ $file->file_name }}" class="ref-action-download p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity" title="Download"><i class="fas fa-download text-sm"></i></a>
                            @if(!empty($file->material_id))
                            <button type="button" class="ref-action-rename p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity" title="Rename" data-file-url="{{ $fileUrl }}" data-file-name="{{ e($file->file_name) }}" data-material-id="{{ $file->material_id }}" data-media-id="{{ $file->media_id }}" data-action="rename"><i class="fas fa-edit text-sm"></i></button>
                            <button type="button" class="ref-action-remove p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-opacity" title="Remove" data-file-url="{{ $fileUrl }}" data-file-name="{{ e($file->file_name) }}" data-material-id="{{ $file->material_id }}" data-media-id="{{ $file->media_id }}" data-action="remove"><i class="fas fa-trash text-sm"></i></button>
                            @endif
                            <button type="button" class="ref-material-menu-btn p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus:outline-none" aria-label="More actions"
                                data-file-url="{{ $fileUrl }}"
                                data-file-name="{{ e($file->file_name) }}"
                                data-material-id="{{ $file->material_id }}"
                                data-media-id="{{ $file->media_id }}">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-2 border-t border-gray-200 flex items-center justify-between flex-wrap gap-2">
            <p class="text-xs text-gray-500">Showing {{ $filesPaginated->firstItem() ?? 0 }}–{{ $filesPaginated->lastItem() ?? 0 }} of {{ $filesPaginated->total() }}</p>
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
            $ext = $file->file_extension ?? strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
            $icon = $fileIconName($ext);
            $iconClass = $fileIconStyle($ext);
            $fileUrl = asset('storage/' . $file->file_path);
            $profileUrl = $file->owner_avatar ? asset('storage/' . $file->owner_avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($file->owner_name) . '&size=32&background=e8f0fe&color=1a73e8';
            $sz = $file->file_size ?? 0;
            $sizeFormatted = $sz >= 1048576 ? number_format($sz / 1048576, 2) . ' MB' : ($sz >= 1024 ? number_format($sz / 1024, 2) . ' KB' : ($sz > 0 ? $sz . ' B' : '—'));
            $displayName = $file->display_name ?? $file->file_name;
            $displayTruncated = Str::length($displayName) > 36 ? Str::limit($displayName, 36) : $displayName;
        @endphp
        <div class="file-card rounded-xl border border-gray-200 p-4 relative group hover:bg-[#f0f7ff] hover:border-[#055498]/30 transition-all">
            <div class="flex flex-col items-center text-center">
                <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="block mb-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto {{ $iconClass }} text-lg">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                </a>
                <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-900 truncate w-full hover:underline" title="{{ $file->file_name }}">{{ $displayTruncated }}</a>
                @if(!empty($file->source_label ?? null))
                    <span class="text-xs text-gray-500 mt-1.5 inline-block truncate w-full" title="{{ $file->source_label }}">{{ $file->source_label }}</span>
                @else
                    <div class="ref-owner-avatar mt-1.5">
                        <img src="{{ $profileUrl }}" alt="{{ $file->owner_name }}" class="h-6 w-6 rounded-full object-cover inline-block cursor-pointer">
                        <div class="ref-avatar-tooltip">{{ $file->owner_name }}</div>
                    </div>
                @endif
                <p class="text-xs text-gray-400 mt-0.5">{{ $file->modified_at->format('M d, Y') }} · {{ $sizeFormatted }}</p>
            </div>
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-0.5">
                <a href="{{ $fileUrl }}" download="{{ $file->file_name }}" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-700" title="Download"><i class="fas fa-download text-xs"></i></a>
                <button type="button" class="ref-material-menu-btn p-1.5 rounded-lg text-gray-400 hover:bg-gray-200"
                    data-file-url="{{ $fileUrl }}"
                    data-file-name="{{ e($file->file_name) }}"
                    data-material-id="{{ $file->material_id }}"
                    data-media-id="{{ $file->media_id }}">
                    <i class="fas fa-ellipsis-v text-xs"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    <div id="filesGridPagination" class="{{ ($filesView ?? 'list') === 'grid' ? '' : 'hidden' }} mt-4 flex items-center justify-between flex-wrap gap-2">
        <p class="text-xs text-gray-500">Showing {{ $filesPaginated->firstItem() ?? 0 }}–{{ $filesPaginated->lastItem() ?? 0 }} of {{ $filesPaginated->total() }}</p>
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
    var activeCls = ['bg-[#055498]', 'text-white'];
    var inactCls = 'text-gray-500';
    if (view === 'grid' && gridEl) {
        listEl.classList.add('hidden');
        gridEl.classList.remove('hidden');
        if (gridPagEl) gridPagEl.classList.remove('hidden');
        if (listBtn) { activeCls.forEach(function(c) { listBtn.classList.remove(c); }); listBtn.classList.add(inactCls); }
        if (gridBtn) { inactCls.split(' ').forEach(function(c) { gridBtn.classList.remove(c); }); activeCls.forEach(function(c) { gridBtn.classList.add(c); }); }
    } else {
        listEl.classList.remove('hidden');
        if (gridEl) gridEl.classList.add('hidden');
        if (gridPagEl) gridPagEl.classList.add('hidden');
        if (listBtn) { inactCls.split(' ').forEach(function(c) { listBtn.classList.remove(c); }); activeCls.forEach(function(c) { listBtn.classList.add(c); }); }
        if (gridBtn) { activeCls.forEach(function(c) { gridBtn.classList.remove(c); }); gridBtn.classList.add(inactCls); }
    }
})();
function setFilesView(v) {
    localStorage.setItem('refMaterialsFilesView', v);
    var listEl = document.getElementById('filesListView');
    var gridEl = document.getElementById('filesGridView');
    var gridPagEl = document.getElementById('filesGridPagination');
    var listBtn = document.getElementById('filesViewListBtn');
    var gridBtn = document.getElementById('filesViewGridBtn');
    var activeCls = ['bg-[#055498]', 'text-white'];
    var inactCls = 'text-gray-500';
    if (v === 'grid' && gridEl) {
        listEl.classList.add('hidden');
        gridEl.classList.remove('hidden');
        if (gridPagEl) gridPagEl.classList.remove('hidden');
        if (listBtn) { activeCls.forEach(function(c) { listBtn.classList.remove(c); }); listBtn.classList.add(inactCls); }
        if (gridBtn) { inactCls.split(' ').forEach(function(c) { gridBtn.classList.remove(c); }); activeCls.forEach(function(c) { gridBtn.classList.add(c); }); }
    } else {
        listEl.classList.remove('hidden');
        if (gridEl) gridEl.classList.add('hidden');
        if (gridPagEl) gridPagEl.classList.add('hidden');
        if (listBtn) { inactCls.split(' ').forEach(function(c) { listBtn.classList.remove(c); }); activeCls.forEach(function(c) { listBtn.classList.add(c); }); }
        if (gridBtn) { activeCls.forEach(function(c) { gridBtn.classList.remove(c); }); gridBtn.classList.add(inactCls); }
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
    var renameRemoveMarkup = (materialId && materialId !== '0') ? (
        '<button type="button" data-action="rename" data-material-id="' + materialId + '" data-media-id="' + mediaId + '" data-file-name="' + (fileName || '').replace(/"/g, '&quot;') + '"><i class="fas fa-edit text-gray-500 w-4"></i> Rename</button>' +
        '<button type="button" class="menu-item-danger" data-action="remove" data-material-id="' + materialId + '" data-media-id="' + mediaId + '" data-file-name="' + (fileName || '').replace(/"/g, '&quot;') + '"><i class="fas fa-trash w-4"></i> Remove</button>'
    ) : '';
    menu.innerHTML =
        openMarkup +
        '<a href="' + safeUrl + '" download="' + safeName + '"><i class="fas fa-download text-gray-500 w-4"></i> Download</a>' +
        renameRemoveMarkup;
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
    // Inline action buttons (Rename, Remove) on row hover
    var page = document.getElementById('refMaterialsPage');
    if (page) {
        page.addEventListener('click', function(ev) {
            var renameBtn = ev.target.closest('.ref-action-rename');
            var removeBtn = ev.target.closest('.ref-action-remove');
            if (renameBtn) {
                ev.preventDefault();
                ev.stopPropagation();
                var mid = parseInt(renameBtn.getAttribute('data-material-id'), 10);
                var vid = parseInt(renameBtn.getAttribute('data-media-id'), 10);
                var fn = renameBtn.getAttribute('data-file-name') || '';
                renameFile(mid, vid, fn);
            }
            if (removeBtn) {
                ev.preventDefault();
                ev.stopPropagation();
                var mid = parseInt(removeBtn.getAttribute('data-material-id'), 10);
                var vid = parseInt(removeBtn.getAttribute('data-media-id'), 10);
                var fn = removeBtn.getAttribute('data-file-name') || '';
                deleteFile(mid, vid, fn);
            }
        });
    }
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
            var origText = textEl ? textEl.textContent : 'Download all';
            btn.disabled = true;
            if (icon) { icon.className = 'ref-download-all-icon fas fa-spinner fa-spin'; }
            if (textEl) { textEl.textContent = 'Preparing...'; }
            // Use direct navigation so server gets same cookies/session as the page (fixes server vs localhost)
            var w = window.open(url, '_blank', 'noopener,noreferrer');
            var t = setTimeout(function() {
                btn.disabled = false;
                if (icon) { icon.className = 'ref-download-all-icon fas fa-file-archive'; }
                if (textEl) { textEl.textContent = origText; }
                if (w && !w.closed) w.close();
            }, 3000);
            if (w) {
                w.addEventListener('load', function() {
                    clearTimeout(t);
                    btn.disabled = false;
                    if (icon) { icon.className = 'ref-download-all-icon fas fa-file-archive'; }
                    if (textEl) { textEl.textContent = origText; }
                    try { w.close(); } catch (e) {}
                });
                w.addEventListener('error', function() {
                    clearTimeout(t);
                    btn.disabled = false;
                    if (icon) { icon.className = 'ref-download-all-icon fas fa-file-archive'; }
                    if (textEl) { textEl.textContent = origText; }
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Download', text: 'Popup was blocked or download failed. Allow popups and try again, or right‑click the button and open in a new tab.' });
                });
            } else {
                clearTimeout(t);
                btn.disabled = false;
                if (icon) { icon.className = 'ref-download-all-icon fas fa-file-archive'; }
                if (textEl) { textEl.textContent = origText; }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'question', title: 'Download', text: 'Popup was blocked. Open download in this tab?', showCancelButton: true, confirmButtonText: 'Open' }).then(function(r) {
                        if (r && r.isConfirmed) window.location.href = url;
                    });
                } else {
                    window.location.href = url;
                }
            }
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
