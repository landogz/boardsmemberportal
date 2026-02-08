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

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-4 sm:mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Master Slider</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Manage landing page carousel slides (title, subtitle, image or video background). Max upload: 100MB per file.</p>
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
                @foreach($slides as $slide)
                <li class="flex items-center gap-4 p-4 hover:bg-gray-50" data-id="{{ $slide->id }}">
                    <span class="cursor-move text-gray-400 hover:text-gray-600" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                    <div class="flex-1 min-w-0 flex items-center gap-4">
                        @if($slide->media)
                            @if($slide->media_type === 'video')
                                <div class="flex-shrink-0 w-20 h-12 bg-gray-200 rounded overflow-hidden flex items-center justify-center">
                                    <i class="fas fa-video text-gray-500"></i>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $slide->media->file_path) }}" alt="" class="w-20 h-12 object-cover rounded flex-shrink-0">
                            @endif
                        @else
                            <div class="w-20 h-12 bg-gray-100 rounded flex items-center justify-center flex-shrink-0"><i class="fas fa-image text-gray-400"></i></div>
                        @endif
                        <div class="min-w-0">
                            <div class="font-medium text-gray-900">{{ $slide->title }}</div>
                            @if($slide->subtitle)
                                <div class="text-sm text-gray-500 truncate">{{ $slide->subtitle }}</div>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $slide->media_type === 'video' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">{{ $slide->media_type }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.banner-slides.edit', $slide) }}" class="px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded">Edit</a>
                        <form action="{{ route('admin.banner-slides.destroy', $slide) }}" method="POST" class="inline" onsubmit="return confirm('Remove this slide?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded">Delete</button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
            <p class="p-3 text-xs text-gray-500 border-t border-gray-200">Drag the <i class="fas fa-grip-vertical mx-0.5"></i> handle to reorder slides. Order is used on the landing page carousel.</p>
        @else
            <div class="text-center py-12">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No slides yet</p>
                <p class="text-sm text-gray-400 mt-1">Add slides to show on the landing page carousel.</p>
                <a href="{{ route('admin.banner-slides.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg text-white font-medium" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-plus"></i> Add Slide
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
        handle: '.cursor-move',
        animation: 150,
        onEnd: function() {
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
