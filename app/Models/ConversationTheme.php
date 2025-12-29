<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationTheme extends Model
{
    protected $fillable = [
        'user1_id',
        'user2_id',
        'theme',
    ];

    /**
     * Get the first user
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get or create theme for a conversation between two users
     * Handles order-independent user pairs
     */
    public static function getOrCreateForConversation($userId1, $userId2)
    {
        // Normalize user IDs (always store smaller ID first)
        $userIds = [min($userId1, $userId2), max($userId1, $userId2)];
        
        return static::firstOrCreate(
            [
                'user1_id' => $userIds[0],
                'user2_id' => $userIds[1],
            ],
            [
                'theme' => null,
            ]
        );
    }

    /**
     * Get theme for a conversation between two users
     */
    public static function getThemeForConversation($userId1, $userId2)
    {
        $userIds = [min($userId1, $userId2), max($userId1, $userId2)];
        
        $conversationTheme = static::where('user1_id', $userIds[0])
            ->where('user2_id', $userIds[1])
            ->first();
        
        return $conversationTheme ? $conversationTheme->theme : null;
    }
}
