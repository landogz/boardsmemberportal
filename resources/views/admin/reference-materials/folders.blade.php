@extends('admin.layout')

@section('title', 'Reference Materials')

@php
    $pageTitle = 'Reference Materials';
    $hideDefaultActions = true;
    $currentSort = $sort ?? 'date';
    $currentDir = $dir ?? 'desc';
@endphp

@push('styles')
<style>
    .drive-search-wrap {
        background: #f1f3f4;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        max-width: 720px;
    }
    .drive-search-wrap:focus-within { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
    .drive-search-wrap input { background: transparent; border: none; }
    .drive-search-wrap input:focus { outline: none; }
    .drive-row:hover { background: #f8f9fa; }
    .drive-table th { font-weight: 500; color: #5f6368; font-size: 0.75rem; }
    .drive-sort-btn { color: #5f6368; padding: 0.25rem 0.5rem; border-radius: 4px; }
    .drive-sort-btn:hover { background: rgba(0,0,0,0.06); color: #202124; }
    .drive-sort-btn.active { color: #1a73e8; }
    .view-toggle-btn { padding: 0.5rem; border-radius: 50%; color: #5f6368; }
    .view-toggle-btn:hover { background: rgba(0,0,0,0.06); color: #202124; }
    .view-toggle-btn.active { color: #1a73e8; background: rgba(26, 115, 232, 0.08); }
    .folder-icon-list { width: 40px; height: 40px; border-radius: 10px; background: #fcc934; color: #5f6368; display: flex; align-items: center; justify-content: center; }
    .folder-card { transition: all 0.15s ease; }
    .folder-card:hover { background: #f8f9fa; }
    .folder-icon-grid { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #055498 0%, #0ea5e9 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6 bg-white min-h-screen">
    {{-- Search bar - Drive style --}}
    <div class="mb-6">
        <form action="{{ route('admin.reference-materials.index') }}" method="get" class="drive-search-wrap flex items-center gap-3">
            <i class="fas fa-search text-gray-500"></i>
            <input type="search" name="q" value="{{ request()->query('q', $q ?? '') }}" placeholder="Search in Reference Materials" class="flex-1 py-2 text-sm text-gray-900 placeholder-gray-500">
            @if(request()->query('q'))
                <a href="{{ route('admin.reference-materials.index') }}" class="text-gray-400 hover:text-gray-600" title="Clear search"><i class="fas fa-times-circle"></i></a>
            @endif
        </form>
    </div>

    {{-- My Drive style header + filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-3 border-b border-gray-200">
        <h1 class="text-lg font-normal text-gray-900">Reference Materials</h1>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Sort --}}
            <div class="flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50/50 overflow-hidden">
                @php
                    $baseUrl = route('admin.reference-materials.index');
                    $params = request()->only(['q']);
                @endphp
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'name', 'dir' => ($currentSort === 'name' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="drive-sort-btn text-xs {{ $currentSort === 'name' ? 'active' : '' }}">Name</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'date', 'dir' => ($currentSort === 'date' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="drive-sort-btn text-xs {{ $currentSort === 'date' ? 'active' : '' }}">Meeting date</a>
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($params, ['sort' => 'items', 'dir' => ($currentSort === 'items' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}" class="drive-sort-btn text-xs {{ $currentSort === 'items' ? 'active' : '' }}">Items</a>
            </div>
            {{-- View toggle --}}
            <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50/50 p-0.5">
                <button type="button" onclick="setView('list')" id="viewListBtn" class="view-toggle-btn {{ ($view ?? 'grid') === 'list' ? 'active' : '' }}" title="List view"><i class="fas fa-list"></i></button>
                <button type="button" onclick="setView('grid')" id="viewGridBtn" class="view-toggle-btn {{ ($view ?? 'grid') === 'grid' ? 'active' : '' }}" title="Grid view"><i class="fas fa-th-large"></i></button>
            </div>
        </div>
    </div>

    {{-- List view --}}
    <div id="listView" class="{{ ($view ?? 'grid') === 'list' ? '' : 'hidden' }} bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full drive-table">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-4 w-12"></th>
                    <th class="text-left py-3 px-4">Name</th>
                    <th class="text-left py-3 px-4">Meeting date</th>
                    <th class="text-left py-3 px-4">Items</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($notices as $notice)
                <tr class="drive-row border-b border-gray-100">
                    <td class="py-3 px-4">
                        <div class="folder-icon-list">
                            <i class="fas fa-folder"></i>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <a href="{{ route('admin.reference-materials.index', ['notice' => $notice->id]) }}" class="text-gray-900 font-medium hover:underline focus:outline-none focus:underline">
                            {{ $notice->title }}
                        </a>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-600">
                        @if($notice->meeting_date)
                            {{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-600">
                        @php
                            $items = $notice->reference_material_items_count ?? $notice->reference_materials_count ?? 0;
                        @endphp
                        {{ $items }} {{ Str::plural('item', $items) }}
                    </td>
                    <td class="py-3 px-4">
                        <a href="{{ route('admin.reference-materials.index', ['notice' => $notice->id]) }}" class="text-gray-400 hover:text-gray-600 p-1 rounded" title="Open"><i class="fas fa-chevron-right text-xs"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-500">
                        @if(request()->query('q')) No folders match your search. <a href="{{ route('admin.reference-materials.index') }}" class="text-blue-600 hover:underline">Clear search</a>
                        @else No meeting notices yet. Meeting notices will appear here as folders.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Grid view --}}
    <div id="gridView" class="{{ ($view ?? 'grid') === 'grid' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @forelse($notices as $notice)
            <a href="{{ route('admin.reference-materials.index', ['notice' => $notice->id]) }}" class="folder-card flex items-center gap-4 rounded-xl border border-gray-200 p-4 hover:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <div class="folder-icon-grid flex-shrink-0">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-gray-900 truncate" title="{{ $notice->title }}">{{ $notice->title }}</h3>
                    @if($notice->meeting_date)
                        <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($notice->meeting_date)->format('M d, Y') }}</p>
                    @endif
                    @php
                        $items = $notice->reference_material_items_count ?? $notice->reference_materials_count ?? 0;
                    @endphp
                    <p class="text-xs text-gray-400 mt-1">{{ $items }} {{ Str::plural('item', $items) }}</p>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-gray-50 rounded-xl border border-gray-200 p-12 text-center">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-600">@if(request()->query('q')) No folders match your search. @else No meeting notices yet. @endif</p>
                @if(request()->query('q')) <a href="{{ route('admin.reference-materials.index') }}" class="text-blue-600 hover:underline text-sm mt-1 inline-block">Clear search</a> @endif
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
        document.getElementById('viewListBtn').classList.add('active');
        document.getElementById('viewGridBtn').classList.remove('active');
    } else {
        document.getElementById('gridView').classList.remove('hidden');
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('viewGridBtn').classList.add('active');
        document.getElementById('viewListBtn').classList.remove('active');
    }
})();
function setView(v) {
    localStorage.setItem('refMaterialsView', v);
    document.getElementById('listView').classList.toggle('hidden', v !== 'list');
    document.getElementById('gridView').classList.toggle('hidden', v !== 'grid');
    document.getElementById('viewListBtn').classList.toggle('active', v === 'list');
    document.getElementById('viewGridBtn').classList.toggle('active', v === 'grid');
}
</script>
@endsection
