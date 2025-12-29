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
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

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
}
