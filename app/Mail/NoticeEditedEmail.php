<?php

namespace App\Mail;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeEditedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notice;
    public $user;
    public $noticeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Notice $notice, User $user)
    {
        $this->notice = $notice;
        $this->user = $user;
        // Generate absolute URL to login page with redirect to notices
        $baseUrl = config('app.url');
        $redirectPath = in_array($user->privilege, ['admin', 'consec']) 
            ? '/admin/notices/' . $notice->id 
            : '/notices/' . $notice->id;
        $this->noticeUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($redirectPath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notice Updated: ' . $this->notice->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice-edited',
            with: [
                'notice' => $this->notice,
                'user' => $this->user,
                'noticeUrl' => $this->noticeUrl,
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
