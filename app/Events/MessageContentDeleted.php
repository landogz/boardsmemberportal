<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageContentDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    /** @var string Conversation id: receiver user id for single chat, or "group_{id}" for group */
    public $conversationId;
    /** @var string[] User IDs to notify (receiver for single; all group members except sender for group) */
    public $userIds;

    /**
     * Create a new event instance.
     *
     * @param int $messageId
     * @param string $conversationId e.g. "user-uuid" or "group_123"
     * @param array $userIds User IDs that should receive this event
     */
    public function __construct($messageId, $conversationId, array $userIds)
    {
        $this->messageId = $messageId;
        $this->conversationId = $conversationId;
        $this->userIds = $userIds;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->userIds as $uid) {
            $channels[] = new PrivateChannel('user.' . $uid);
        }
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.content.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->messageId,
            'conversation_id' => $this->conversationId,
        ];
    }
}
