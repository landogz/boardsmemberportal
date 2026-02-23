@extends('admin.layout')

@section('title', 'Edit Slide')

@push('styles')
<style>
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
    .opacity-preview {
        background-image: linear-gradient(45deg, #ccc 25%, transparent 25%),
            linear-gradient(-45deg, #ccc 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #ccc 75%),
            linear-gradient(-45deg, transparent 75%, #ccc 75%);
        background-size: 12px 12px;
        background-position: 0 0, 0 6px, 6px -6px, -6px 0;
        background-color: #f3f4f6;
    }
    .color-input-native { position: absolute; width: 0; height: 0; opacity: 0; pointer-events: none; }
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
    $titleColor = old('title_color', $slide->title_color ?? '#ffffff');
    $subtitleColor = old('subtitle_color', $slide->subtitle_color ?? '#e5e7eb');
    $mediaOpacity = old('media_opacity', $slide->media_opacity !== null ? (float) $slide->media_opacity : 1);
    $titleSizeMap = ['sm' => '1.25rem', 'md' => '1.5rem', 'lg' => '2rem', 'xl' => '2.5rem', '2xl' => '3rem'];
    $subtitleSizeMap = ['sm' => '0.875rem', 'md' => '1rem', 'lg' => '1.125rem', 'xl' => '1.25rem'];
    $currentMediaUrl = $slide->media ? asset('storage/' . $slide->media->file_path) : null;
    $currentMediaIsVideo = $slide->media_type === 'video' && $slide->media;
@endphp

@section('content')
<div class="p-4 lg:p-6 pb-24 lg:pb-8">
    <div class="mb-6">
        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit Carousel Slide</h2>
        <p class="text-sm text-gray-500 mt-1">Update title, subtitle, and optionally replace the background. Max file size: 100MB.</p>
    </div>

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-7">
            <form action="{{ route('admin.banner-slides.update', $slide) }}" method="POST" enctype="multipart/form-data" id="banner-slide-form" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Card: Text content --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h3 class="text-sm font-semibold text-gray-800">Text content</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Title and subtitle shown on the slide.</p>
                    </div>
                    <div class="p-5 space-y-4">
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
                    </div>
                </div>

                {{-- Card: Styling --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h3 class="text-sm font-semibold text-gray-800">Styling</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Colors, font sizes, and background overlay.</p>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title color</label>
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <input type="color" id="title_color_swatch" value="{{ $titleColor }}" class="color-input-native" aria-hidden="true">
                                        <button type="button" id="title_color_trigger" class="block h-11 w-14 rounded-lg border-2 border-gray-300 shadow-inner cursor-pointer hover:border-[#055498] transition-colors" style="background-color: {{ $titleColor }};" title="Click to pick color"></button>
                                    </div>
                                    <input type="text" id="title_color" name="title_color" value="{{ $titleColor }}" maxlength="20"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none font-mono text-sm"
                                        placeholder="#ffffff">
                                </div>
                                @error('title_color')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle color</label>
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <input type="color" id="subtitle_color_swatch" value="{{ $subtitleColor }}" class="color-input-native" aria-hidden="true">
                                        <button type="button" id="subtitle_color_trigger" class="block h-11 w-14 rounded-lg border-2 border-gray-300 shadow-inner cursor-pointer hover:border-[#055498] transition-colors" style="background-color: {{ $subtitleColor }};" title="Click to pick color"></button>
                                    </div>
                                    <input type="text" id="subtitle_color" name="subtitle_color" value="{{ $subtitleColor }}" maxlength="20"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none font-mono text-sm"
                                        placeholder="#e5e7eb">
                                </div>
                                @error('subtitle_color')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="title_font_size" class="block text-sm font-medium text-gray-700 mb-2">Title size</label>
                                <select id="title_font_size" name="title_font_size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                                    @foreach(\App\Models\BannerSlide::titleSizeOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ old('title_font_size', $slide->title_font_size ?? 'lg') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('title_font_size')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label for="subtitle_font_size" class="block text-sm font-medium text-gray-700 mb-2">Subtitle size</label>
                                <select id="subtitle_font_size" name="subtitle_font_size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none">
                                    @foreach(\App\Models\BannerSlide::subtitleSizeOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ old('subtitle_font_size', $slide->subtitle_font_size ?? 'md') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('subtitle_font_size')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">See the preview on the right to see how your title and subtitle will look.</p>

                        <div>
                            <label for="media_opacity" class="block text-sm font-medium text-gray-700 mb-2">Background overlay opacity</label>
                            <div class="flex items-center gap-4">
                                <div class="opacity-preview rounded-lg border border-gray-200 overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                                    <div id="media_opacity_preview" class="w-full h-full bg-gray-900/80 transition-opacity" style="opacity: {{ $mediaOpacity }};"></div>
                                </div>
                                <div class="flex-1 min-w-0 flex items-center gap-3">
                                    <input type="range" id="media_opacity" name="media_opacity" min="0" max="1" step="0.05" value="{{ $mediaOpacity }}" class="opacity-range flex-1 min-w-0">
                                    <span id="media_opacity_display" class="text-sm font-medium text-gray-600 w-12">{{ round((float) $mediaOpacity * 100) }}%</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Higher = darker overlay on the image/video so text stands out more.</p>
                            @error('media_opacity')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Card: Background media --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h3 class="text-sm font-semibold text-gray-800">Background media</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Replace optional. Same type for all. Leave empty to keep current.</p>
                    </div>
                    <div class="p-5 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Background type *</label>
                            <div class="inline-flex p-1 rounded-full bg-gray-100 border border-gray-200" role="group" aria-label="Background type">
                                <label class="cursor-pointer">
                                    <input type="radio" name="media_type" value="image" {{ old('media_type', $slide->media_type) === 'image' ? 'checked' : '' }} class="sr-only peer">
                                    <span class="block px-5 py-2.5 rounded-full text-sm font-medium transition-all peer-checked:bg-white peer-checked:shadow peer-checked:text-[#055498] peer-checked:ring-1 peer-checked:ring-gray-200 text-gray-600">Image</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="media_type" value="video" {{ old('media_type', $slide->media_type) === 'video' ? 'checked' : '' }} class="sr-only peer">
                                    <span class="block px-5 py-2.5 rounded-full text-sm font-medium transition-all peer-checked:bg-white peer-checked:shadow peer-checked:text-[#055498] peer-checked:ring-1 peer-checked:ring-gray-200 text-gray-600">Video</span>
                                </label>
                            </div>
                            @error('media_type')<span class="text-red-500 text-sm block mt-1">{{ $message }}</span>@enderror
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <label for="media_file" class="block text-sm font-medium text-gray-700 mb-2">Desktop</label>
                            @if($slide->media)
                                <p class="text-xs text-gray-600 mb-2">Current: @if($slide->media_type === 'video') {{ $slide->media->file_name }} @else <img src="{{ asset('storage/' . $slide->media->file_path) }}" alt="" class="inline-block max-h-16 rounded object-cover"> @endif</p>
                            @endif
                            <p class="text-xs text-gray-500 mb-2">Shown on large screens. Recommended: 1920 × 460 px. Leave empty to keep.</p>
                            <input type="file" id="media_file" name="media_file" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg,video/quicktime"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none bg-white">
                            @error('media_file')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>

                        <details class="group border border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
                            <summary class="px-4 py-3 cursor-pointer list-none flex items-center justify-between text-sm font-medium text-gray-700 hover:bg-gray-100/80">
                                <span>Tablet (optional)</span>
                                <span class="text-gray-400 group-open:rotate-180 transition-transform inline-block"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                            </summary>
                            <div class="px-4 pb-4 pt-0">
                                @if($slide->mediaTablet)
                                    <p class="text-xs text-gray-600 mb-2">Current: @if($slide->media_type === 'video') {{ $slide->mediaTablet->file_name }} @else <img src="{{ asset('storage/' . $slide->mediaTablet->file_path) }}" alt="" class="inline-block max-h-16 rounded object-cover"> @endif</p>
                                @else
                                    <p class="text-xs text-gray-500 mb-2">Uses desktop file if not set.</p>
                                @endif
                                <p class="text-xs text-gray-500 mb-2">769px–1024px. Recommended: 1024 × 400 px.</p>
                                <input type="file" id="media_file_tablet" name="media_file_tablet" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg,video/quicktime"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none bg-white">
                                @error('media_file_tablet')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </details>

                        <details class="group border border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
                            <summary class="px-4 py-3 cursor-pointer list-none flex items-center justify-between text-sm font-medium text-gray-700 hover:bg-gray-100/80">
                                <span>Mobile (optional)</span>
                                <span class="text-gray-400 group-open:rotate-180 transition-transform inline-block"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                            </summary>
                            <div class="px-4 pb-4 pt-0">
                                @if($slide->mediaMobile)
                                    <p class="text-xs text-gray-600 mb-2">Current: @if($slide->media_type === 'video') {{ $slide->mediaMobile->file_name }} @else <img src="{{ asset('storage/' . $slide->mediaMobile->file_path) }}" alt="" class="inline-block max-h-16 rounded object-cover"> @endif</p>
                                @else
                                    <p class="text-xs text-gray-500 mb-2">Uses tablet or desktop if not set.</p>
                                @endif
                                <p class="text-xs text-gray-500 mb-2">≤768px. Recommended: 768 × 350 px.</p>
                                <input type="file" id="media_file_mobile" name="media_file_mobile" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/ogg,video/quicktime"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#055498] focus:border-[#055498] outline-none bg-white">
                                @error('media_file_mobile')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        </details>

                        <p class="text-xs text-gray-500">Ensure php.ini upload_max_filesize and post_max_size are at least 100M for large files.</p>
                    </div>
                </div>
            </form>
        </div>

        {{-- Live preview --}}
        <div class="lg:col-span-5 mt-8 lg:mt-0">
            <div class="lg:sticky lg:top-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/80">
                        <h3 class="text-sm font-semibold text-gray-800">Slide preview</h3>
                        <p class="text-xs text-gray-500">Updates as you type and change options.</p>
                    </div>
                    <div class="p-4">
                        <div id="slide-preview" class="relative rounded-lg overflow-hidden bg-gray-200 min-h-[220px] flex items-center justify-center">
                            <div id="preview-bg-wrap" class="absolute inset-0 flex items-center justify-center opacity-90">
                                <div id="preview-bg-placeholder" class="absolute inset-0 bg-gradient-to-br from-slate-400 to-slate-600 {{ $currentMediaUrl ? 'hidden' : '' }}"></div>
                                <img id="preview-bg-image" class="absolute inset-0 w-full h-full object-cover {{ $currentMediaUrl && !$currentMediaIsVideo ? '' : 'hidden' }}" src="{{ $currentMediaUrl && !$currentMediaIsVideo ? $currentMediaUrl : '' }}" alt="">
                                <video id="preview-bg-video" class="absolute inset-0 w-full h-full object-cover {{ $currentMediaIsVideo ? '' : 'hidden' }}" muted loop playsinline src="{{ $currentMediaIsVideo ? $currentMediaUrl : '' }}"></video>
                            </div>
                            <div id="preview-overlay" class="absolute inset-0 bg-black transition-opacity" style="opacity: {{ 1 - (float) $mediaOpacity }};"></div>
                            <div class="relative z-10 text-center px-6 py-4 max-w-lg mx-auto">
                                <div id="preview-title" class="font-bold leading-tight" style="color: {{ $titleColor }}; font-size: {{ $titleSizeMap[$slide->title_font_size ?? 'lg'] ?? '2rem' }};">{{ old('title', $slide->title) ?: 'Your title' }}</div>
                                <div id="preview-subtitle" class="mt-2" style="color: {{ $subtitleColor }}; font-size: {{ $subtitleSizeMap[$slide->subtitle_font_size ?? 'md'] ?? '1rem' }};">{{ old('subtitle', $slide->subtitle) ?: 'Your subtitle' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 shadow-lg safe-area-pb">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row gap-3 sm:justify-end sm:items-center">
        <a href="{{ route('admin.banner-slides.index') }}" class="order-2 sm:order-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center font-medium">Cancel</a>
        <button type="submit" form="banner-slide-form" class="order-1 sm:order-2 w-full sm:w-auto px-6 py-3 rounded-lg font-semibold text-white shadow-md hover:shadow-lg transition-all" style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">Update Slide</button>
    </div>
</div>

<script>
(function() {
    var titleSizeMap = @json($titleSizeMap);
    var subtitleSizeMap = @json($subtitleSizeMap);

    function $(id) { return document.getElementById(id); }
    function byName(n) { return document.querySelector('[name="' + n + '"]'); }

    function bindColor(swatchId, triggerId, hexId) {
        var swatch = $(swatchId);
        var trigger = $(triggerId);
        var hex = $(hexId);
        if (!swatch || !trigger || !hex) return;
        trigger.addEventListener('click', function() { swatch.click(); });
        swatch.addEventListener('input', function() {
            var v = swatch.value;
            hex.value = v;
            trigger.style.backgroundColor = v;
        });
        hex.addEventListener('input', function() {
            var v = hex.value;
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
                swatch.value = v;
                trigger.style.backgroundColor = v;
            }
        });
    }
    bindColor('title_color_swatch', 'title_color_trigger', 'title_color');
    bindColor('subtitle_color_swatch', 'subtitle_color_trigger', 'subtitle_color');

    var opacityEl = $('media_opacity');
    var opacityDisplay = $('media_opacity_display');
    var opacityPreview = $('media_opacity_preview');
    if (opacityEl) {
        function updateOpacity() {
            var v = parseFloat(opacityEl.value);
            if (opacityDisplay) opacityDisplay.textContent = Math.round(v * 100) + '%';
            if (opacityPreview) opacityPreview.style.opacity = String(v);
        }
        opacityEl.addEventListener('input', updateOpacity);
        updateOpacity();
    }

    var mediaTypeRadios = document.querySelectorAll('input[name="media_type"]');
    function updatePillStyles() {
        mediaTypeRadios.forEach(function(r) {
            var label = r.closest('label');
            if (!label) return;
            var span = label.querySelector('span:last-child');
            if (span) {
                if (r.checked) {
                    span.classList.add('bg-white', 'shadow', 'text-[#055498]', 'ring-1', 'ring-gray-200');
                    span.classList.remove('text-gray-600');
                } else {
                    span.classList.remove('bg-white', 'shadow', 'text-[#055498]', 'ring-1', 'ring-gray-200');
                    span.classList.add('text-gray-600');
                }
            }
        });
    }
    mediaTypeRadios.forEach(function(r) { r.addEventListener('change', updatePillStyles); });
    updatePillStyles();

    var previewTitle = $('preview-title');
    var previewSubtitle = $('preview-subtitle');
    var previewOverlay = $('preview-overlay');

    function updatePreviewText() {
        var title = ($('title') && $('title').value) || 'Your title';
        var subtitle = ($('subtitle') && $('subtitle').value) || 'Your subtitle';
        var titleColor = ($('title_color') && $('title_color').value) || '#ffffff';
        var subtitleColor = ($('subtitle_color') && $('subtitle_color').value) || '#e5e7eb';
        var titleSize = ($('title_font_size') && $('title_font_size').value) || 'lg';
        var subtitleSize = ($('subtitle_font_size') && $('subtitle_font_size').value) || 'md';
        if (previewTitle) {
            previewTitle.textContent = title;
            previewTitle.style.color = titleColor;
            previewTitle.style.fontSize = titleSizeMap[titleSize] || '2rem';
        }
        if (previewSubtitle) {
            previewSubtitle.textContent = subtitle;
            previewSubtitle.style.color = subtitleColor;
            previewSubtitle.style.fontSize = subtitleSizeMap[subtitleSize] || '1rem';
        }
    }

    function updatePreviewOverlay() {
        if (!previewOverlay || !opacityEl) return;
        var v = parseFloat(opacityEl.value);
        previewOverlay.style.opacity = String(1 - v);
    }

    ['title', 'subtitle', 'title_color', 'subtitle_color', 'title_font_size', 'subtitle_font_size'].forEach(function(id) {
        var el = $(id);
        if (el) el.addEventListener('input', updatePreviewText);
        if (el) el.addEventListener('change', updatePreviewText);
    });
    if (opacityEl) opacityEl.addEventListener('input', updatePreviewOverlay);

    updatePreviewText();
    updatePreviewOverlay();

    var mediaFile = $('media_file');
    var previewBgPlaceholder = $('preview-bg-placeholder');
    var previewBgImage = $('preview-bg-image');
    var previewBgVideo = $('preview-bg-video');
    var mediaTypeImage = document.querySelector('input[name="media_type"][value="image"]');
    var isImage = function() { return !mediaTypeImage || mediaTypeImage.checked; };

    if (mediaFile) {
        mediaFile.addEventListener('change', function() {
            var file = this.files && this.files[0];
            if (!file) {
                previewBgImage.classList.add('hidden');
                previewBgImage.src = '';
                previewBgVideo.classList.add('hidden');
                previewBgVideo.pause();
                previewBgVideo.src = '';
                previewBgPlaceholder.classList.remove('hidden');
                return;
            }
            if (isImage() && file.type.indexOf('image/') === 0) {
                var url = URL.createObjectURL(file);
                previewBgImage.onload = function() { URL.revokeObjectURL(url); };
                previewBgImage.src = url;
                previewBgImage.classList.remove('hidden');
                previewBgVideo.classList.add('hidden');
                previewBgVideo.src = '';
                previewBgPlaceholder.classList.add('hidden');
            } else if (!isImage() && file.type.indexOf('video/') === 0) {
                var url = URL.createObjectURL(file);
                previewBgVideo.src = url;
                previewBgVideo.classList.remove('hidden');
                previewBgVideo.play().catch(function(){});
                previewBgImage.classList.add('hidden');
                previewBgImage.src = '';
                previewBgPlaceholder.classList.add('hidden');
            } else {
                previewBgPlaceholder.classList.remove('hidden');
                previewBgImage.classList.add('hidden');
                previewBgVideo.classList.add('hidden');
            }
        });
    }
    mediaTypeRadios.forEach(function(r) {
        r.addEventListener('change', function() {
            if (mediaFile && mediaFile.files && mediaFile.files[0]) mediaFile.dispatchEvent(new Event('change'));
        });
    });

    if (previewBgVideo && previewBgVideo.src) previewBgVideo.play().catch(function(){});
})();
</script>
@endsection
