<?php

namespace App\Models;

use App\Events\NotificationUnreadCountUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'url',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Broadcast when a new notification is created
        static::created(function ($notification) {
            $count = static::where('user_id', $notification->user_id)
                ->where('is_read', false)
                ->count();
            broadcast(new NotificationUnreadCountUpdated($notification->user_id, $count));
        });
    }

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark all notifications for a user that are related to a given notice as read.
     * Used when user accepts, declines, or views a notice.
     * @param int|string $userId User ID (int or UUID string depending on schema)
     */
    public static function markNoticeAsReadForUser(int|string $userId, int $noticeId): void
    {
        $updated = static::where('user_id', $userId)
            ->where('type', 'notice')
            ->where('data->notice_id', (string) $noticeId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if ($updated > 0) {
            $count = static::where('user_id', $userId)->where('is_read', false)->count();
            broadcast(new NotificationUnreadCountUpdated($userId, $count));
        }
    }
}
