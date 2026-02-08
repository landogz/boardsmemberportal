@extends('admin.layout')

@section('title', 'Edit Slide')

@push('styles')
<style>
    /* Make range input track and thumb visible */
    input[type="range"].opacity-range {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 4px;
        background: #e5e7eb;
        outline: none;
    }
    input[type="range"].opacity-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #055498;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input[type="range"].opacity-range::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #055498;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
</style>
@endpush

@php
    $pageTitle = 'Edit Slide';
    $headerActions = [];
    $headerActions[] = [
        'url' => route('admin.banner-slides.index'),
        'text' => 'Back to Master Slider',
        'icon' => 'fas fa-arrow-left',
        'class' => 'px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition-colors inline-flex items-center'
    ];
    $hideDefaultActions = false;
@endphp

@section('content')
<div class="p-4 lg:p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Carousel Slide</h2>
        <p class="text-gray-600 mt-1">Update title, subtitle, and optionally replace the background. Max file size: 100MB.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.banner-slides.update', $slide) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" id="title" name="title" required value="{{ old('title', $slide->title) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                @error('title')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $slide->subtitle) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                @error('subtitle')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            @php
                $titleColor = old('title_color', $slide->title_color ?? '#ffffff');
                $subtitleColor = old('subtitle_color', $slide->subtitle_color ?? '#e5e7eb');
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="title_color" class="block text-sm font-medium text-gray-700 mb-2">Title color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="title_color_swatch" value="{{ $titleColor }}" class="h-10 w-14 rounded border border-gray-300 cursor-pointer">
                        <input type="text" id="title_color" name="title_color" value="{{ $titleColor }}" maxlength="20"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                            placeholder="#ffffff">
                    </div>
                    @error('title_color')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="subtitle_color" class="block text-sm font-medium text-gray-700 mb-2">Subtitle color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="subtitle_color_swatch" value="{{ $subtitleColor }}" class="h-10 w-14 rounded border border-gray-300 cursor-pointer">
                        <input type="text" id="subtitle_color" name="subtitle_color" value="{{ $subtitleColor }}" maxlength="20"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none"
                            placeholder="#e5e7eb">
                    </div>
                    @error('subtitle_color')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>
            <script>
            document.getElementById('title_color_swatch').addEventListener('input', function(e) { document.getElementById('title_color').value = e.target.value; });
            document.getElementById('title_color').addEventListener('input', function(e) { var v = e.target.value; if (/^#[0-9A-Fa-f]{6}$/.test(v)) document.getElementById('title_color_swatch').value = v; });
            document.getElementById('subtitle_color_swatch').addEventListener('input', function(e) { document.getElementById('subtitle_color').value = e.target.value; });
            document.getElementById('subtitle_color').addEventListener('input', function(e) { var v = e.target.value; if (/^#[0-9A-Fa-f]{6}$/.test(v)) document.getElementById('subtitle_color_swatch').value = v; });
            </script>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="title_font_size" class="block text-sm font-medium text-gray-700 mb-2">Title font size</label>
                    <select id="title_font_size" name="title_font_size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                        @foreach(\App\Models\BannerSlide::titleSizeOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('title_font_size', $slide->title_font_size ?? 'lg') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('title_font_size')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="subtitle_font_size" class="block text-sm font-medium text-gray-700 mb-2">Subtitle font size</label>
                    <select id="subtitle_font_size" name="subtitle_font_size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                        @foreach(\App\Models\BannerSlide::subtitleSizeOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('subtitle_font_size', $slide->subtitle_font_size ?? 'md') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('subtitle_font_size')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Background type *</label>
                <div class="flex gap-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="media_type" value="image" {{ old('media_type', $slide->media_type) === 'image' ? 'checked' : '' }} class="rounded border-gray-300 text-[#055498] focus:ring-[#055498]">
                        <span class="ml-2">Image</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="media_type" value="video" {{ old('media_type', $slide->media_type) === 'video' ? 'checked' : '' }} class="rounded border-gray-300 text-[#055498] focus:ring-[#055498]">
                        <span class="ml-2">Video</span>
                    </label>
                </div>
                @error('media_type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            @php
                $mediaOpacity = old('media_opacity', $slide->media_opacity !== null ? (float) $slide->media_opacity : 1);
            @endphp
            <div>
                <label for="media_opacity" class="block text-sm font-medium text-gray-700 mb-2">Image / video opacity</label>
                <div class="flex items-center gap-3">
                    <input type="range" id="media_opacity" name="media_opacity" min="0" max="1" step="0.05" value="{{ $mediaOpacity }}" class="opacity-range flex-1 min-w-0">
                    <span id="media_opacity_display" class="text-sm font-medium text-gray-600 w-12">{{ round((float) $mediaOpacity * 100) }}%</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">0% = fully transparent, 100% = fully opaque.</p>
                @error('media_opacity')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <script>document.getElementById('media_opacity').addEventListener('input', function() { document.getElementById('media_opacity_display').textContent = Math.round(parseFloat(this.value) * 100) + '%'; });</script>
            </div>

            <p class="text-sm font-medium text-gray-700 mb-2">Background by device — replace optional, max 100MB per file.</p>
            <div class="space-y-4">
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <label for="media_file" class="block text-sm font-medium text-gray-700 mb-2">Desktop — &gt;1024px</label>
                    @if($slide->media)
                        <p class="text-xs text-gray-600 mb-2">Current: @if($slide->media_type === 'video') {{ $slide->media->file_name }} @else <img src="{{ asset('storage/' . $slide->media->file_path) }}" alt="" class="inline-block max-h-16 rounded object-cover"> @endif</p>
                    @endif
                    <input type="file" id="media_file" name="media_file" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg,video/quicktime"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] outline-none bg-white">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 1920×460. Leave empty to keep.</p>
                    @error('media_file')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <label for="media_file_tablet" class="block text-sm font-medium text-gray-700 mb-2">Tablet (optional) — 769px–1024px</label>
                    @if($slide->mediaTablet)
                        <p class="text-xs text-gray-600 mb-2">Current: @if($slide->media_type === 'video') {{ $slide->mediaTablet->file_name }} @else <img src="{{ asset('storage/' . $slide->mediaTablet->file_path) }}" alt="" class="inline-block max-h-16 rounded object-cover"> @endif</p>
                    @else
                        <p class="text-xs text-gray-500 mb-2">Uses desktop file if not set.</p>
                    @endif
                    <input type="file" id="media_file_tablet" name="media_file_tablet" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg,video/quicktime"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] outline-none bg-white">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 1024×400.</p>
                    @error('media_file_tablet')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <label for="media_file_mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile (optional) — ≤768px</label>
                    @if($slide->mediaMobile)
                        <p class="text-xs text-gray-600 mb-2">Current: @if($slide->media_type === 'video') {{ $slide->mediaMobile->file_name }} @else <img src="{{ asset('storage/' . $slide->mediaMobile->file_path) }}" alt="" class="inline-block max-h-16 rounded object-cover"> @endif</p>
                    @else
                        <p class="text-xs text-gray-500 mb-2">Uses tablet or desktop if not set.</p>
                    @endif
                    <input type="file" id="media_file_mobile" name="media_file_mobile" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg,video/quicktime"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] outline-none bg-white">
                    <p class="mt-1 text-xs text-gray-500">Recommended: 768×350.</p>
                    @error('media_file_mobile')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 text-white rounded-lg font-medium" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">Update Slide</button>
                <a href="{{ route('admin.banner-slides.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
