@extends('admin.layout')

@section('title', 'Master Slider')

@php
    $pageTitle = 'Master Slider';
    $headerActions = [];
    if (Auth::user()->privilege === 'admin') {
        $headerActions[] = [
            'url' => route('admin.banner-slides.create'),
            'text' => 'Add Slide',
            'icon' => 'fas fa-plus',
            'class' => 'px-4 py-2 text-white rounded-lg font-semibold transition-all duration-300',
            'style' => 'background: linear-gradient(135deg, #055498 0%, #123a60 100%);'
        ];
    }
    $hideDefaultActions = false;
@endphp

@push('styles')
<style>
    .slide-drag-handle {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        border-radius: 6px;
        transition: color 0.2s, background 0.2s;
    }
    .slide-row:hover .slide-drag-handle,
    .slide-drag-handle:hover {
        color: #055498;
        background: #eff6ff;
    }
    .slide-thumb {
        width: 120px;
        min-width: 120px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .slide-thumb-placeholder {
        width: 120px;
        min-width: 120px;
        height: 70px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #9ca3af;
    }
    .badge-image { background-color: #dbeafe; color: #1d4ed8; }
    .badge-video { background-color: #ede9fe; color: #6d28d9; }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Master Slider</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Manage landing page carousel slides (title, subtitle, image or video background). Max upload: 100MB per file.</p>
        @if($slides->count() > 0)
        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-grip-vertical mr-1"></i> Drag the handle to reorder slides. Order is used on the landing page carousel.</p>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($slides->count() > 0)
            <ul id="slideList" class="divide-y divide-gray-200">
                @foreach($slides as $index => $slide)
                @php $slideNumber = $index + 1; @endphp
                <li class="slide-row flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors" data-id="{{ $slide->id }}">
                    <span class="slide-drag-handle cursor-move flex-shrink-0" title="Drag to reorder"><i class="fas fa-grip-vertical text-sm"></i></span>
                    <span class="slide-order flex-shrink-0 w-7 text-sm font-semibold text-gray-400" title="Order">{{ $slideNumber }}</span>
                    <div class="flex-1 min-w-0 flex items-center gap-4">
                        @if($slide->media)
                            @if($slide->media_type === 'video')
                                <div class="slide-thumb-placeholder">
                                    <i class="fas fa-video text-lg"></i>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $slide->media->file_path) }}" alt="" class="slide-thumb">
                            @endif
                        @else
                            <div class="slide-thumb-placeholder"><i class="fas fa-image text-lg"></i></div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-gray-900">{{ $slide->title }}</div>
                            @if($slide->subtitle)
                                <div class="text-sm text-gray-500 truncate">{{ $slide->subtitle }}</div>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1 {{ $slide->media_type === 'video' ? 'badge-video' : 'badge-image' }}">{{ ucfirst($slide->media_type) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.banner-slides.edit', $slide) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-gray-300 text-gray-600 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.banner-slides.destroy', $slide) }}" method="POST" class="inline" onsubmit="return confirm('Remove this slide?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:border-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
        @else
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center mb-6">
                    <i class="fas fa-images text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No slides yet</h3>
                <p class="text-gray-500 max-w-sm mb-6">Add slides to show on the landing page carousel. Each slide can have a title, subtitle, and image or video background.</p>
                <a href="{{ route('admin.banner-slides.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white font-semibold shadow-md hover:shadow-lg transition-all" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-plus"></i> Add your first slide
                </a>
            </div>
        @endif
    </div>
</div>

@if($slides->count() > 1)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var list = document.getElementById('slideList');
    if (!list) return;
    new Sortable(list, {
        handle: '.slide-drag-handle',
        animation: 150,
        onEnd: function() {
            Array.from(list.querySelectorAll('li')).forEach(function(li, i) {
                var el = li.querySelector('.slide-order');
                if (el) el.textContent = i + 1;
            });
            var order = Array.from(list.querySelectorAll('li')).map(function(li) { return parseInt(li.getAttribute('data-id'), 10); });
            fetch('{{ route("admin.banner-slides.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });
});
</script>
@endpush
@endif
@endsection
