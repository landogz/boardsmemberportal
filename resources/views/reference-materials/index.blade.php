<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reference Materials - Board Members Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
    @include('components.header-footer-styles')
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    <div class="min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 md:px-8 py-6">
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Reference Materials</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Browse all meeting reference materials and files distributed to you by CONSEC.</p>
            </div>

            <form action="{{ route('reference-materials.index') }}" method="get" class="mb-6 flex items-center gap-3 rounded-xl border border-gray-300 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <i class="fas fa-search text-gray-400"></i>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q', $q ?? '') }}"
                    placeholder="Search meetings or folders"
                    class="flex-1 bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-500 dark:text-white"
                >
                @if(request('q', $q ?? ''))
                    <a href="{{ route('reference-materials.index') }}" class="text-sm font-medium text-[#055498] hover:underline">Clear</a>
                @endif
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($notices as $notice)
                    <a
                        href="{{ route('reference-materials.index', ['notice' => $notice->id]) }}"
                        class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:border-[#055498]/40 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#055498] text-white">
                                <i class="fas fa-folder text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-base font-semibold text-gray-900 transition-colors group-hover:text-[#055498] dark:text-white" title="{{ $notice->title }}">{{ $notice->title }}</h2>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $notice->meeting_date ? \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') : 'No meeting date' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4 text-sm dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-300">
                                {{ $notice->reference_material_items_count ?? 0 }} {{ Str::plural('item', $notice->reference_material_items_count ?? 0) }}
                            </span>
                            <span class="text-[#055498]"><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border-2 border-dashed border-gray-200 bg-white px-6 py-16 text-center dark:border-gray-700 dark:bg-gray-800">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">No reference material folders are available for your meetings yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @include('components.footer')
    @include('components.pdf-modal')
</body>
</html>
