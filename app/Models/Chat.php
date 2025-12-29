<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'parent_id',
        'group_id',
        'message',
        'attachments',
        'timestamp',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'attachments' => 'array', // Cast to array for JSON storage
    ];

    /**
     * Get the sender user
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver user
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the parent message (if this is a reply)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'parent_id');
    }

    /**
     * Get replies to this message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Chat::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get reactions for this message
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'chat_id');
    }

    /**
     * Get the group chat (if this is a group message)
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupChat::class, 'group_id');
    }

    /**
     * Check if this is a group message
     */
    public function isGroupMessage(): bool
    {
        return !is_null($this->group_id);
    }

    // Note: attachments is now stored as a JSON array, not a foreign key
    // Individual attachments are fetched via MediaLibrary::find() in the controller
}

