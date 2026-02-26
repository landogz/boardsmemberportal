<?php

namespace App\Mail;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;

    public $user;

    public $announcementUrl;

    public $isUpdate;

    /**
     * Create a new message instance.
     *
     * @param  bool  $isUpdate  When true, subject and body indicate the announcement was updated (not new).
     */
    public function __construct(Announcement $announcement, User $user, bool $isUpdate = false)
    {
        $this->announcement = $announcement;
        $this->user = $user;
        $this->isUpdate = $isUpdate;
        // Generate absolute URL to login page with redirect to announcements section
        $baseUrl = config('app.url');
        $this->announcementUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode('/#announcements');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $categoryLabel = $this->announcement->category_label ?? 'Announcement';

        $subject = $this->isUpdate
            ? 'Updated ' . $categoryLabel . ': ' . $this->announcement->title
            : 'New ' . $categoryLabel . ': ' . $this->announcement->title;

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.announcement',
            with: [
                'announcement' => $this->announcement,
                'user' => $this->user,
                'announcementUrl' => $this->announcementUrl,
                'isUpdate' => $this->isUpdate,
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

