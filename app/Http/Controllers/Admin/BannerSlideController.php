<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerSlide;
use App\Models\MediaLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerSlideController extends Controller
{
    /** Max upload size for carousel media: 100MB */
    private const MAX_UPLOAD_BYTES = 100 * 1024 * 1024;

    /** Upload a file to storage and media library; return MediaLibrary id or null. */
    private function uploadMediaFile($file, string $mediaType): ?int
    {
        if (!$file || $file->getSize() > self::MAX_UPLOAD_BYTES) {
            return null;
        }
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = 'banner_slides/' . $fileName;
        Storage::disk('public')->put($filePath, file_get_contents($file));
        $media = MediaLibrary::create([
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_path' => $filePath,
            'uploaded_by' => Auth::id(),
        ]);
        return $media->id;
    }

    private function ensureAdmin(): void
    {
        if (Auth::user()->privilege !== 'admin') {
            abort(403, 'Only administrators can manage the Master Slider.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $slides = BannerSlide::with(['media', 'mediaTablet', 'mediaMobile'])->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.banner-slides.index', compact('slides'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.banner-slides.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'title_color' => 'nullable|string|max:20',
            'subtitle_color' => 'nullable|string|max:20',
            'title_font_size' => 'nullable|string|max:20|in:sm,md,lg,xl,2xl',
            'subtitle_font_size' => 'nullable|string|max:20|in:sm,md,lg,xl',
            'media_opacity' => 'nullable|numeric|min:0|max:1',
            'media_type' => 'required|in:image,video',
            'media_url' => 'nullable|string|max:1000',
            'media_file' => 'nullable|file',
            'media_file_tablet' => 'nullable|file',
            'media_file_mobile' => 'nullable|file',
        ], [
            'title.required' => 'Title is required.',
            'media_type.required' => 'Please choose Image or Video.',
        ]);

        // Get video URL from request (form may send it as media_url)
        $mediaUrl = trim((string) $request->input('media_url', ''));
        $mediaUrl = $mediaUrl === '' ? null : $mediaUrl;

        if ($mediaUrl !== null && ! BannerSlide::isSupportedVideoUrl($mediaUrl)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['media_url' => 'Please enter a valid YouTube or Vimeo link.']);
        }

        if ($validated['media_type'] === 'image') {
            $request->validate(['media_file' => 'required|file|mimes:jpeg,jpg,png,gif,webp'], [], ['media_file' => 'Desktop image']);
        } else {
            // Video: require either uploaded file OR video URL
            if ($mediaUrl === null && ! $request->hasFile('media_file')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['media_file' => 'Please upload a video file or paste a YouTube / Vimeo link.']);
            }
            if ($mediaUrl === null) {
                $request->validate([
                    'media_file' => 'required|file|mimes:mp4,mov,webm,ogg',
                    'media_file_tablet' => 'nullable|mimes:mp4,mov,webm,ogg',
                    'media_file_mobile' => 'nullable|mimes:mp4,mov,webm,ogg',
                ], [], ['media_file' => 'Desktop file', 'media_file_tablet' => 'Tablet file', 'media_file_mobile' => 'Mobile file']);
            }
        }

        $mediaId = null;
        $mediaIdTablet = null;
        $mediaIdMobile = null;
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            if ($file->getSize() > self::MAX_UPLOAD_BYTES) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'media_file' => 'File size must not exceed 100MB. Ensure upload_max_filesize and post_max_size in php.ini are at least 100M.',
                    ]);
            }
            $mediaId = $this->uploadMediaFile($file, $validated['media_type']);
            $mediaIdTablet = $request->hasFile('media_file_tablet') ? $this->uploadMediaFile($request->file('media_file_tablet'), $validated['media_type']) : null;
            $mediaIdMobile = $request->hasFile('media_file_mobile') ? $this->uploadMediaFile($request->file('media_file_mobile'), $validated['media_type']) : null;
        }

        $maxOrder = BannerSlide::max('sort_order') ?? 0;
        BannerSlide::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'title_color' => $validated['title_color'] ?? null,
            'subtitle_color' => $validated['subtitle_color'] ?? null,
            'title_font_size' => $validated['title_font_size'] ?? null,
            'subtitle_font_size' => $validated['subtitle_font_size'] ?? null,
            'media_opacity' => isset($validated['media_opacity']) ? (float) $validated['media_opacity'] : null,
            'media_type' => $validated['media_type'],
            'media_url' => $mediaUrl,
            'media_id' => $mediaId,
            'media_id_tablet' => $mediaIdTablet,
            'media_id_mobile' => $mediaIdMobile,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.banner-slides.index')
            ->with('success', 'Slide added successfully.');
    }

    public function edit(BannerSlide $banner_slide)
    {
        $this->ensureAdmin();
        $slide = $banner_slide->load(['media', 'mediaTablet', 'mediaMobile']);
        return view('admin.banner-slides.edit', compact('slide'));
    }

    public function update(Request $request, BannerSlide $banner_slide)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'title_color' => 'nullable|string|max:20',
            'subtitle_color' => 'nullable|string|max:20',
            'title_font_size' => 'nullable|string|max:20|in:sm,md,lg,xl,2xl',
            'subtitle_font_size' => 'nullable|string|max:20|in:sm,md,lg,xl',
            'media_opacity' => 'nullable|numeric|min:0|max:1',
            'media_type' => 'required|in:image,video',
            'media_url' => 'nullable|string|max:1000',
            'media_file' => 'nullable|file',
            'media_file_tablet' => 'nullable|file',
            'media_file_mobile' => 'nullable|file',
        ]);

        // Get video URL from request
        $mediaUrl = trim((string) $request->input('media_url', ''));
        $mediaUrl = $mediaUrl === '' ? null : $mediaUrl;

        if ($mediaUrl !== null && ! BannerSlide::isSupportedVideoUrl($mediaUrl)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['media_url' => 'Please enter a valid YouTube or Vimeo link.']);
        }

        if ($validated['media_type'] === 'image') {
            $request->validate([
                'media_file' => 'nullable|mimes:jpeg,jpg,png,gif,webp',
                'media_file_tablet' => 'nullable|mimes:jpeg,jpg,png,gif,webp',
                'media_file_mobile' => 'nullable|mimes:jpeg,jpg,png,gif,webp',
            ], [], ['media_file' => 'Desktop', 'media_file_tablet' => 'Tablet', 'media_file_mobile' => 'Mobile']);
        } else {
            $request->validate([
                'media_file' => 'nullable|mimes:mp4,mov,webm,ogg',
                'media_file_tablet' => 'nullable|mimes:mp4,mov,webm,ogg',
                'media_file_mobile' => 'nullable|mimes:mp4,mov,webm,ogg',
            ], [], ['media_file' => 'Desktop', 'media_file_tablet' => 'Tablet', 'media_file_mobile' => 'Mobile']);
        }

        $slide = $banner_slide;

        $mediaId = $slide->media_id;
        $mediaIdTablet = $slide->media_id_tablet;
        $mediaIdMobile = $slide->media_id_mobile;
        $mediaUrlToSave = $mediaUrl;

        if ($mediaUrl !== null) {
            $mediaId = null;
            $mediaIdTablet = null;
            $mediaIdMobile = null;
        }

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            if ($file->getSize() > self::MAX_UPLOAD_BYTES) {
                return redirect()->back()->withInput()->withErrors(['media_file' => 'File size must not exceed 100MB.']);
            }
            $mediaId = $this->uploadMediaFile($file, $validated['media_type']);
            $mediaUrlToSave = null; // uploaded file takes precedence
        }
        if ($request->hasFile('media_file_tablet')) {
            $file = $request->file('media_file_tablet');
            if ($file->getSize() > self::MAX_UPLOAD_BYTES) {
                return redirect()->back()->withInput()->withErrors(['media_file_tablet' => 'File size must not exceed 100MB.']);
            }
            $mediaIdTablet = $this->uploadMediaFile($file, $validated['media_type']);
        }
        if ($request->hasFile('media_file_mobile')) {
            $file = $request->file('media_file_mobile');
            if ($file->getSize() > self::MAX_UPLOAD_BYTES) {
                return redirect()->back()->withInput()->withErrors(['media_file_mobile' => 'File size must not exceed 100MB.']);
            }
            $mediaIdMobile = $this->uploadMediaFile($file, $validated['media_type']);
        }

        $slide->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'title_color' => $validated['title_color'] ?? null,
            'subtitle_color' => $validated['subtitle_color'] ?? null,
            'title_font_size' => $validated['title_font_size'] ?? null,
            'subtitle_font_size' => $validated['subtitle_font_size'] ?? null,
            'media_opacity' => isset($validated['media_opacity']) ? (float) $validated['media_opacity'] : null,
            'media_type' => $validated['media_type'],
            'media_url' => $mediaUrlToSave,
            'media_id' => $mediaId,
            'media_id_tablet' => $mediaIdTablet,
            'media_id_mobile' => $mediaIdMobile,
        ]);

        return redirect()->route('admin.banner-slides.index')
            ->with('success', 'Slide updated successfully.');
    }

    public function destroy(BannerSlide $banner_slide)
    {
        $this->ensureAdmin();
        $banner_slide->delete();
        return redirect()->route('admin.banner-slides.index')
            ->with('success', 'Slide removed.');
    }

    public function reorder(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:banner_slides,id',
        ]);
        foreach ($request->order as $index => $id) {
            BannerSlide::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
    }
}
