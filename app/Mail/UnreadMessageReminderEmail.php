<?php

namespace App\Mail;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnreadMessageReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $chatMessage;
    public $sender;
    public $messagesUrl;
    public $isGroupMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Chat $message, User $sender)
    {
        $this->user = $user;
        $this->chatMessage = $message;
        $this->sender = $sender;
        $this->isGroupMessage = !is_null($message->group_id);
        // Load group relationship if it's a group message
        if ($this->isGroupMessage) {
            $this->chatMessage->load('group');
        }
        // Generate absolute URL to messages page based on user privilege
        $baseUrl = config('app.url');
        $messagesPath = ($user->privilege === 'admin' || $user->privilege === 'consec') ? '/admin/messages' : '/messages';
        $this->messagesUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($messagesPath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You Have Unread Messages - Board Members Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.unread-message-reminder',
            with: [
                'user' => $this->user,
                'chatMessage' => $this->chatMessage,
                'sender' => $this->sender,
                'messagesUrl' => $this->messagesUrl,
                'isGroupMessage' => $this->isGroupMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

