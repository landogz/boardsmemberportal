<?php

namespace App\Mail;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticePostponementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notice;
    public $user;
    public $noticeUrl;
    public $acceptUrl;
    public $declineUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Notice $notice, User $user)
    {
        $this->notice = $notice;
        $this->user = $user;
        $baseUrl = config('app.url');
        $redirectPath = in_array($user->privilege, ['admin', 'consec'])
            ? '/admin/notices/' . $notice->id
            : '/notices/' . $notice->id;
        $this->noticeUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($redirectPath);
        $userNoticePath = '/notices/' . $notice->id;
        $this->acceptUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($userNoticePath . '?action=accept');
        $this->declineUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($userNoticePath . '?action=decline');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notice of Postponement: ' . $this->notice->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice-postponement',
            with: [
                'notice' => $this->notice,
                'user' => $this->user,
                'noticeUrl' => $this->noticeUrl,
                'acceptUrl' => $this->acceptUrl,
                'declineUrl' => $this->declineUrl,
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
