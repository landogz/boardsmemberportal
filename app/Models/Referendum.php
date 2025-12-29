<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referendum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'attachments',
        'expires_at',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'attachments' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the referendum
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all votes for this referendum
     */
    public function votes()
    {
        return $this->hasMany(ReferendumVote::class);
    }

    /**
     * Get accept votes
     */
    public function acceptVotes()
    {
        return $this->votes()->where('vote', 'accept');
    }

    /**
     * Get decline votes
     */
    public function declineVotes()
    {
        return $this->votes()->where('vote', 'decline');
    }

    /**
     * Get all comments for this referendum
     */
    public function comments()
    {
        return $this->hasMany(ReferendumComment::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get all comments including replies
     */
    public function allComments()
    {
        return $this->hasMany(ReferendumComment::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get users who have access to this referendum
     */
    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'referendum_user_access', 'referendum_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Check if referendum is expired
     */
    public function isExpired()
    {
        return $this->expires_at < now();
    }

    /**
     * Check if user can vote (not expired and has access)
     */
    public function canVote($userId)
    {
        if ($this->isExpired()) {
            return false;
        }

        return $this->allowedUsers()->where('users.id', $userId)->exists();
    }

    /**
     * Check if user can comment (not expired and has access)
     */
    public function canComment($userId)
    {
        if ($this->isExpired()) {
            return false;
        }

        return $this->allowedUsers()->where('users.id', $userId)->exists();
    }

    /**
     * Check if user has voted
     */
    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's vote
     */
    public function getUserVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}
