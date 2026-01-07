<?php

namespace App\Mail;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeAcceptedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notice;
    public $user; // The user who accepted
    public $creator; // The notice creator
    public $noticeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Notice $notice, User $user, User $creator)
    {
        $this->notice = $notice;
        $this->user = $user;
        $this->creator = $creator;
        
        // Generate absolute URL to notice page based on creator's privilege
        $baseUrl = config('app.url');
        $redirectPath = in_array($creator->privilege, ['admin', 'consec']) 
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
            subject: 'Notice Accepted: ' . $this->notice->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice-accepted',
            with: [
                'notice' => $this->notice,
                'user' => $this->user,
                'creator' => $this->creator,
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

