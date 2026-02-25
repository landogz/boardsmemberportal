@extends('admin.layout')

@section('title', 'Reference Materials')

@php
    $pageTitle = 'Reference Materials';
    $hideDefaultActions = false;
    $currentSort = $sort ?? 'date';
    $currentDir = $dir ?? 'desc';
    $baseUrl = route('admin.reference-materials.index');
    $params = request()->only(['q']);
@endphp

@section('content')
<div class="p-4 lg:p-6 bg-white min-h-screen">
    {{-- Search + Filter bar: wide container, border, shadow --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-3">
        <form action="{{ route('admin.reference-materials.index') }}" method="get" class="flex-1 w-full min-w-0 max-w-2xl flex items-center gap-3 rounded-xl border border-gray-300 bg-white px-4 py-2.5 shadow-sm focus-within:ring-2 focus-within:ring-[#055498]/20 focus-within:border-[#055498] transition-shadow">
            <i class="fas fa-search text-gray-400 flex-shrink-0"></i>
            <input type="search" name="q" value="{{ request()->query('q', $q ?? '') }}" placeholder="Search in Reference Materials" class="flex-1 min-w-0 py-1.5 text-sm text-gray-900 placeholder-gray-500 bg-transparent border-0 focus:outline-none focus:ring-0">
            @if(request()->query('q'))
                <a href="{{ route('admin.reference-materials.index') }}" class="text-gray-400 hover:text-gray-600 flex-shrink-0" title="Clear search"><i class="fas fa-times-circle"></i></a>
            @endif
        </form>
        {{-- Filter dropdown (sort by date / name / items) --}}
        <details class="relative flex-shrink-0 group/details">
            <summary class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-medium shadow-sm hover:bg-gray-50 transition-colors cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                <i class="fas fa-filter"></i>
                <span>Filter</span>
                <i class="fas fa-chevron-down text-xs transition-transform group-open/details:rotate-180"></i>
            </summary>
            <div class="absolute right-0 mt-2 w-48 py-1 bg-white rounded-xl border border-gray-200 shadow-lg z-10">
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'date', 'dir' => ($currentSort === 'date' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">By meeting date</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'name', 'dir' => ($currentSort === 'name' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">By name</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'items', 'dir' => ($currentSort === 'items' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">By items</a>
            </div>
        </details>
    </div>

    {{-- Toolbar: sort pills + view toggle --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mb-4 pb-3 border-b border-gray-200">
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Sort pills: active = pill highlight --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'name', 'dir' => ($currentSort === 'name' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center px-3 py-1.5 text-sm rounded-full transition-colors {{ $currentSort === 'name' ? 'bg-[#055498] text-white font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Name</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'date', 'dir' => ($currentSort === 'date' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center px-3 py-1.5 text-sm rounded-full transition-colors {{ $currentSort === 'date' ? 'bg-[#055498] text-white font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Meeting date</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'items', 'dir' => ($currentSort === 'items' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center px-3 py-1.5 text-sm rounded-full transition-colors {{ $currentSort === 'items' ? 'bg-[#055498] text-white font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Items</a>
            </div>
            {{-- View toggle --}}
            <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50/80 p-0.5">
                <button type="button" onclick="setView('list')" id="viewListBtn" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors {{ ($view ?? 'grid') === 'list' ? 'bg-[#055498] text-white hover:bg-[#044a85] hover:text-white' : '' }}" title="List view"><i class="fas fa-list"></i></button>
                <button type="button" onclick="setView('grid')" id="viewGridBtn" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors {{ ($view ?? 'grid') === 'grid' ? 'bg-[#055498] text-white hover:bg-[#044a85] hover:text-white' : '' }}" title="Grid view"><i class="fas fa-th-large"></i></button>
            </div>
        </div>
    </div>

    {{-- List view: compact table with tighter rows --}}
    <div id="listView" class="{{ ($view ?? 'grid') === 'list' ? '' : 'hidden' }} bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/80">
                    <th class="text-left py-2 px-4 w-12 text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Meeting date</th>
                    <th class="text-left py-2 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="w-10 py-2 px-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($notices as $notice)
                @php
                    $items = $notice->reference_material_items_count ?? $notice->reference_materials_count ?? 0;
                    $rowUrl = route('admin.reference-materials.index', ['notice' => $notice->id]);
                @endphp
                <tr role="button" tabindex="0" data-href="{{ $rowUrl }}" class="list-row border-b border-gray-100 hover:bg-[#f0f7ff] cursor-pointer transition-colors" onclick="window.location.href=this.dataset.href" onkeydown="if(event.key==='Enter'){window.location.href=this.dataset.href}">
                    <td class="py-2 px-4">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 bg-[#055498] text-white text-sm">
                            <i class="fas fa-folder"></i>
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        <a href="{{ $rowUrl }}" class="text-gray-900 font-medium hover:underline focus:outline-none focus:underline" onclick="event.stopPropagation()">
                            {{ $notice->title }}
                        </a>
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-600">
                        @if($notice->meeting_date)
                            {{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-2 px-4 text-sm">
                        @if($items === 0)
                            <span class="text-gray-500 italic">0 items</span>
                            <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">No items</span>
                        @else
                            <span class="text-gray-600">{{ $items }} {{ Str::plural('item', $items) }}</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        <a href="{{ $rowUrl }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors" title="Open" onclick="event.stopPropagation()"><i class="fas fa-chevron-right text-sm font-semibold"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-500">
                        @if(request()->query('q'))
                            No folders match your search. <a href="{{ route('admin.reference-materials.index') }}" class="text-[#055498] hover:underline font-medium">Clear search</a>
                        @else
                            <p class="mb-4">No meeting notices yet. Meeting notices will appear here as folders.</p>
                            @if(Auth::user()->hasPermission('create notices'))
                                <a href="{{ route('admin.notices.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold shadow-sm transition-all hover:opacity-90" style="background-color: #055498;">
                                    <i class="fas fa-plus"></i>
                                    <span>Add New Reference Material</span>
                                </a>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Grid view: cards with date badge, item count, brand-blue folder icon --}}
    <div id="gridView" class="{{ ($view ?? 'grid') === 'grid' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($notices as $notice)
            @php
                $items = $notice->reference_material_items_count ?? $notice->reference_materials_count ?? 0;
            @endphp
            <a href="{{ route('admin.reference-materials.index', ['notice' => $notice->id]) }}" class="group flex flex-col rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-[#055498]/40 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#055498] focus:ring-offset-2 transition-all duration-200">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-[#055498] text-white text-lg">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 truncate group-hover:text-[#055498] transition-colors" title="{{ $notice->title }}">{{ $notice->title }}</h3>
                        @if($notice->meeting_date)
                            <span class="inline-block mt-1.5 px-2 py-0.5 text-xs font-medium rounded-md bg-gray-100 text-gray-600">{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        @if($items === 0)
                            <span class="text-xs text-gray-500 italic">0 items</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">No items</span>
                        @else
                            <span class="text-xs text-gray-500">{{ $items }} {{ Str::plural('item', $items) }}</span>
                        @endif
                    </div>
                    <span class="text-gray-400 group-hover:text-[#055498] transition-colors"><i class="fas fa-chevron-right text-sm"></i></span>
                </div>
            </a>
        @empty
            {{-- Empty state: illustration + CTA --}}
            <div class="col-span-full flex flex-col items-center justify-center py-16 px-4 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50">
                <div class="w-20 h-20 rounded-full bg-gray-200/80 flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                </div>
                <p class="text-gray-600 text-center max-w-sm mb-1">
                    @if(request()->query('q'))
                        No folders match your search.
                    @else
                        No meeting notices yet. Create a notice to add reference material folders.
                    @endif
                </p>
                @if(request()->query('q'))
                    <a href="{{ route('admin.reference-materials.index') }}" class="text-[#055498] hover:underline font-medium text-sm mt-1">Clear search</a>
                @else
                    @if(Auth::user()->hasPermission('create notices'))
                        <a href="{{ route('admin.notices.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold shadow-sm transition-all hover:opacity-90" style="background-color: #055498;">
                            <i class="fas fa-plus"></i>
                            <span>Add New Reference Material</span>
                        </a>
                    @endif
                @endif
            </div>
        @endforelse
    </div>

</div>

<script>
(function() {
    var view = localStorage.getItem('refMaterialsView') || 'grid';
    if (view === 'list') {
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('gridView').classList.add('hidden');
        document.getElementById('viewListBtn').classList.add('bg-[#055498]', 'text-white');
        document.getElementById('viewListBtn').classList.remove('text-gray-500');
        document.getElementById('viewGridBtn').classList.remove('bg-[#055498]', 'text-white');
        document.getElementById('viewGridBtn').classList.add('text-gray-500');
    } else {
        document.getElementById('gridView').classList.remove('hidden');
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('viewGridBtn').classList.add('bg-[#055498]', 'text-white');
        document.getElementById('viewGridBtn').classList.remove('text-gray-500');
        document.getElementById('viewListBtn').classList.remove('bg-[#055498]', 'text-white');
        document.getElementById('viewListBtn').classList.add('text-gray-500');
    }
})();
function setView(v) {
    localStorage.setItem('refMaterialsView', v);
    document.getElementById('listView').classList.toggle('hidden', v !== 'list');
    document.getElementById('gridView').classList.toggle('hidden', v !== 'grid');
    var listBtn = document.getElementById('viewListBtn');
    var gridBtn = document.getElementById('viewGridBtn');
    if (v === 'list') {
        listBtn.classList.add('bg-[#055498]', 'text-white'); listBtn.classList.remove('text-gray-500');
        gridBtn.classList.remove('bg-[#055498]', 'text-white'); gridBtn.classList.add('text-gray-500');
    } else {
        gridBtn.classList.add('bg-[#055498]', 'text-white'); gridBtn.classList.remove('text-gray-500');
        listBtn.classList.remove('bg-[#055498]', 'text-white'); listBtn.classList.add('text-gray-500');
    }
}
</script>
@endsection
