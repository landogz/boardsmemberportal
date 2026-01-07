<?php

namespace App\Mail;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeDeclinedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notice;
    public $user; // The user who declined
    public $creator; // The notice creator
    public $declinedReason;
    public $noticeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Notice $notice, User $user, User $creator, string $declinedReason)
    {
        $this->notice = $notice;
        $this->user = $user;
        $this->creator = $creator;
        $this->declinedReason = $declinedReason;
        
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
            subject: 'Notice Declined: ' . $this->notice->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice-declined',
            with: [
                'notice' => $this->notice,
                'user' => $this->user,
                'creator' => $this->creator,
                'declinedReason' => $this->declinedReason,
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

