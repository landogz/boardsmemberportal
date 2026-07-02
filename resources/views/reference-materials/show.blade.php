<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $notice->title }} - Reference Materials</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    @php
        $iconClass = function ($ext, $mime) {
            if (str_starts_with((string) $mime, 'video/')) return 'fa-file-video text-purple-600';
            if ($ext === 'pdf') return 'fa-file-pdf text-red-500';
            if (in_array($ext, ['doc', 'docx'])) return 'fa-file-word text-blue-600';
            if (in_array($ext, ['xls', 'xlsx'])) return 'fa-file-excel text-emerald-600';
            if (in_array($ext, ['ppt', 'pptx'])) return 'fa-file-powerpoint text-amber-600';
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'fa-file-image text-sky-600';
            return 'fa-file-alt text-gray-500';
        };
    @endphp

    <div class="min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 md:px-8 py-6">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <a href="{{ route('reference-materials.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#055498] hover:underline">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Reference Materials</span>
                    </a>
                    <h1 class="mt-3 truncate text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white" title="{{ $notice->title }}">{{ $notice->title }}</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Meeting files, agenda items, approved reference materials, regulations, and resolutions distributed for this meeting.</p>
                </div>
            </div>

            <form action="{{ route('reference-materials.index') }}" method="get" class="mb-6 flex flex-col gap-3 rounded-xl border border-gray-300 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center">
                <input type="hidden" name="notice" value="{{ $notice->id }}">
                <div class="flex flex-1 items-center gap-3 rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-700">
                    <i class="fas fa-search text-gray-400"></i>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q', $q ?? '') }}"
                        placeholder="Search files in this meeting"
                        class="flex-1 bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-500 dark:text-white"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <select name="sort" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                        <option value="modified" {{ ($sort ?? 'modified') === 'modified' ? 'selected' : '' }}>Sort by modified</option>
                        <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Sort by name</option>
                    </select>
                    <select name="dir" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                        <option value="desc" {{ ($dir ?? 'desc') === 'desc' ? 'selected' : '' }}>Newest first</option>
                        <option value="asc" {{ ($dir ?? '') === 'asc' ? 'selected' : '' }}>Oldest first</option>
                    </select>
                    <button type="submit" class="rounded-lg bg-[#055498] px-4 py-2 text-sm font-medium text-white hover:bg-[#123a60]">Apply</button>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($filesPaginated as $file)
                    @php
                        $ext = $file->file_extension ?? strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                        $fileUrl = asset('storage/' . $file->file_path);
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isVideo = str_starts_with((string) $file->file_type, 'video/');
                        $sizeFormatted = $file->file_size >= 1048576
                            ? number_format($file->file_size / 1048576, 2) . ' MB'
                            : ($file->file_size >= 1024 ? number_format($file->file_size / 1024, 2) . ' KB' : ($file->file_size > 0 ? $file->file_size . ' B' : '—'));
                    @endphp
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex h-48 items-center justify-center bg-gray-50 dark:bg-gray-900">
                            @if($isImage)
                                <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}" class="h-full w-full object-cover">
                            @elseif($isVideo)
                                <video controls class="h-full w-full object-cover">
                                    <source src="{{ $fileUrl }}" type="{{ $file->file_type }}">
                                </video>
                            @else
                                <i class="fas {{ $iconClass($ext, $file->file_type) }} text-5xl"></i>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-sm font-semibold text-gray-900 dark:text-white" title="{{ $file->file_name }}">{{ $file->file_name }}</h2>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $file->source_label ?? 'Reference Material' }}</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $file->modified_at ? \Carbon\Carbon::parse($file->modified_at)->format('M d, Y') : '—' }}</span>
                                <span>{{ $sizeFormatted }}</span>
                            </div>
                            <div class="mt-4 flex gap-2">
                                @if(($ext ?? '') === 'pdf')
                                    <button type="button" onclick="openGlobalPdfModal('{{ $fileUrl }}', '{{ addslashes($file->file_name) }}')" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">View</button>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">Open</a>
                                @endif
                                <a href="{{ $fileUrl }}" download="{{ $file->file_name }}" class="flex-1 rounded-lg bg-[#055498] px-3 py-2 text-center text-sm font-medium text-white hover:bg-[#123a60]">Download</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border-2 border-dashed border-gray-200 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-800">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700">
                            <i class="fas fa-file-alt text-2xl"></i>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">No reference materials are available in this meeting folder yet.</p>
                    </div>
                @endforelse
            </div>

            @if($filesPaginated->count() > 0)
                <div class="mt-6">
                    {{ $filesPaginated->links() }}
                </div>
            @endif
        </div>
    </div>

    @include('components.footer')
    @include('components.pdf-modal')
</body>
</html>
