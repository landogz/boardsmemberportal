<?php

namespace App\Mail;

use App\Models\Referendum;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferendumEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $referendum;
    public $referendumUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Referendum $referendum)
    {
        $this->user = $user;
        $this->referendum = $referendum;
        
        // Generate absolute URL to login page with redirect to referendum
        $baseUrl = config('app.url');
        $referendumPath = '/referendums/' . $referendum->id;
        $this->referendumUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($referendumPath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Referendum Available - Board Members Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.referendum',
            with: [
                'user' => $this->user,
                'referendum' => $this->referendum,
                'referendumUrl' => $this->referendumUrl,
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

