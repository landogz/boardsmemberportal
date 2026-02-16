<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'banner_image_id',
        'created_by',
        'status',
        'scheduled_at',
        'category',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Human-readable category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'board_member_activities' => 'Board Member Activities',
            'public', null => 'Public',
            default => ucfirst(str_replace('_', ' ', (string) $this->category)),
        };
    }

    /**
     * Accessor for description (maps to content)
     */
    public function getDescriptionAttribute()
    {
        return $this->content;
    }

    /**
     * Mutator for description (maps to content)
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    /**
     * Description with automatic hyperlinking of plain-text URLs.
     * Converts http/https URLs into <a> tags that open in a new tab.
     * If the content already contains an <a> tag, we assume the editor handled links
     * and return the original HTML to avoid double-wrapping.
     */
    public function getDescriptionWithLinksAttribute()
    {
        $html = $this->description ?? '';

        // If content already has anchor tags, don't try to auto-link
        if (stripos($html, '<a ') !== false) {
            return $html;
        }

        // Work on raw content, auto-link plain http/https URLs
        $pattern = '~(https?://[^\s<]+)~i';

        $callback = function ($matches) {
            $url = $matches[1];
            $escapedUrl = e($url);
            return '<a href="' . $escapedUrl . '" target="_blank" rel="noopener noreferrer">' . $escapedUrl . '</a>';
        };

        return preg_replace_callback($pattern, $callback, $html);
    }

    /**
     * Get the user who created the announcement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the banner image
     */
    public function bannerImage()
    {
        return $this->belongsTo(MediaLibrary::class, 'banner_image_id');
    }

    /**
     * Get users who have access to this announcement
     */
    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'announcement_user_access', 'announcement_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Check if announcement is published
     */
    public function isPublished()
    {
        return $this->status === 'published' && 
               ($this->scheduled_at === null || $this->scheduled_at <= now());
    }

    /**
     * Check if user has access to this announcement
     */
    public function hasUserAccess($userId)
    {
        return $this->allowedUsers()->where('user_id', $userId)->exists();
    }

    /**
     * Scope for published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function($q) {
                        $q->whereNull('scheduled_at')
                          ->orWhere('scheduled_at', '<=', now());
                    });
    }

    /**
     * Scope for draft announcements
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Automatically publish any draft announcements whose scheduled_at
     * is less than or equal to "now".
     *
     * This is a lightweight alternative to a scheduled task and is called
     * from controllers that work with announcements (admin index, calendar, etc.).
     */
    public static function autoPublishScheduled(): void
    {
        static::where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->update(['status' => 'published']);
    }
}
