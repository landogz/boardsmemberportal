<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'title_color',
        'subtitle_color',
        'title_font_size',
        'subtitle_font_size',
        'media_opacity',
        'media_type',
        'media_url',
        'media_id',
        'media_id_tablet',
        'media_id_mobile',
        'sort_order',
        'is_active',
    ];

    /** Font size presets for title (CSS font-size values) */
    public static function titleSizeOptions(): array
    {
        return [
            'sm' => 'Small (1.25rem)',
            'md' => 'Medium (1.5rem)',
            'lg' => 'Large (2rem)',
            'xl' => 'Extra large (2.5rem)',
            '2xl' => '2X large (3rem)',
        ];
    }

    /** Font size presets for subtitle */
    public static function subtitleSizeOptions(): array
    {
        return [
            'sm' => 'Small (0.875rem)',
            'md' => 'Medium (1rem)',
            'lg' => 'Large (1.125rem)',
            'xl' => 'Extra large (1.25rem)',
        ];
    }

    public function getTitleFontSizeCssAttribute(): string
    {
        $map = ['sm' => '1.25rem', 'md' => '1.5rem', 'lg' => '2rem', 'xl' => '2.5rem', '2xl' => '3rem'];
        return $map[$this->title_font_size] ?? '1.75rem';
    }

    public function getSubtitleFontSizeCssAttribute(): string
    {
        $map = ['sm' => '0.875rem', 'md' => '1rem', 'lg' => '1.125rem', 'xl' => '1.25rem'];
        return $map[$this->subtitle_font_size] ?? '1rem';
    }

    protected $casts = [
        'is_active' => 'boolean',
        'media_opacity' => 'float',
    ];

    /** Opacity for image/video background (0–1). Default 1 when null. */
    public function getMediaOpacityValueAttribute(): float
    {
        $v = $this->media_opacity;
        if ($v === null) {
            return 1.0;
        }
        return max(0, min(1, (float) $v));
    }

    public function media()
    {
        return $this->belongsTo(MediaLibrary::class, 'media_id');
    }

    public function mediaTablet()
    {
        return $this->belongsTo(MediaLibrary::class, 'media_id_tablet');
    }

    public function mediaMobile()
    {
        return $this->belongsTo(MediaLibrary::class, 'media_id_mobile');
    }

    /** Get media for breakpoint: 'desktop' | 'tablet' | 'mobile'. Falls back to desktop if not set. */
    public function mediaForBreakpoint(string $breakpoint): ?MediaLibrary
    {
        if ($breakpoint === 'tablet' && $this->media_id_tablet) {
            return $this->mediaTablet ?? $this->media;
        }
        if ($breakpoint === 'mobile' && $this->media_id_mobile) {
            return $this->mediaMobile ?? $this->mediaTablet ?? $this->media;
        }
        return $this->media;
    }

    /** URL of the uploaded file (from media library). Use attributes['media_url'] for YouTube/Vimeo link. */
    public function getMediaFileUrlAttribute(): ?string
    {
        if (!$this->media) {
            return null;
        }
        return asset('storage/' . $this->media->file_path);
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === 'video';
    }

    public function getIsImageAttribute(): bool
    {
        return $this->media_type === 'image';
    }

    /** Check if a URL is a supported video link (YouTube or Vimeo). */
    public static function isSupportedVideoUrl(?string $url): bool
    {
        $url = trim((string) $url);
        if ($url === '') {
            return false;
        }
        return (bool) preg_match('#(?:youtube\.com|youtu\.be|vimeo\.com)#', $url);
    }

    /**
     * Whether this slide uses an external video URL (YouTube, Vimeo, etc.) instead of uploaded file.
     */
    public function getIsVideoUrlAttribute(): bool
    {
        return $this->media_type === 'video' && ! empty($this->media_url);
    }

    /**
     * Get embed URL for YouTube or Vimeo (for iframe). Returns null if not a supported link.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        $url = trim((string) $this->media_url);
        if ($url === '') {
            return null;
        }

        // YouTube: watch, youtu.be, embed, shorts, live (11-char video ID)
        if (preg_match(
            '#(?:youtube\.com/(?:watch\?v=|embed/|shorts/|live/)|youtu\.be/)([a-zA-Z0-9_-]{11})#',
            $url,
            $m
        )) {
            $id = $m[1];
            return 'https://www.youtube.com/embed/' . $id
                . '?autoplay=1&mute=1&loop=1&playlist=' . $id
                . '&controls=0&showinfo=0&rel=0';
        }

        // Vimeo: https://vimeo.com/ID or https://vimeo.com/video/ID
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1]
                . '?autoplay=1&muted=1&loop=1&background=1';
        }

        // Fallback: if it's some other playable URL, just return it as-is
        // so it can still be used in an iframe.
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return null;
    }
}
