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

    public function getMediaUrlAttribute(): ?string
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
}
