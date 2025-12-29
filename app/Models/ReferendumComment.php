<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferendumComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referendum_id',
        'user_id',
        'parent_id',
        'content',
    ];

    /**
     * Get the referendum this comment belongs to
     */
    public function referendum()
    {
        return $this->belongsTo(Referendum::class);
    }

    /**
     * Get the user who made this comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (if this is a reply)
     */
    public function parent()
    {
        return $this->belongsTo(ReferendumComment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment
     */
    public function replies()
    {
        return $this->hasMany(ReferendumComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Check if this is a reply
     */
    public function isReply()
    {
        return $this->parent_id !== null;
    }
}
