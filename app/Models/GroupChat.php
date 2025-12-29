<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupChat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'avatar',
        'theme',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the creator/admin of the group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all members of the group
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    /**
     * Get all admin members
     */
    public function admins(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id')->where('is_admin', true);
    }

    /**
     * Get all messages in this group
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Chat::class, 'group_id')->orderBy('created_at', 'asc');
    }

    /**
     * Check if a user is a member of this group
     */
    public function hasMember($userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Check if a user is an admin of this group
     */
    public function isAdmin($userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('is_admin', true)
            ->exists();
    }

    /**
     * Get member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}

