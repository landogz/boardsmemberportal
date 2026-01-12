<?php

namespace App\Mail;

use App\Models\OfficialDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoardResolutionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resolution;
    public $resolutionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, OfficialDocument $resolution)
    {
        $this->user = $user;
        $this->resolution = $resolution;
        
        // Generate absolute URL to login page with redirect
        // For regular users: /board-issuances
        // For consec: /admin/notifications
        $baseUrl = config('app.url');
        $resolutionPath = ($user->privilege === 'consec' || $user->privilege === 'admin') 
            ? '/admin/notifications' 
            : '/board-issuances';
        $this->resolutionUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($resolutionPath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Board Resolution Published - Board Members Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.board-resolution',
            with: [
                'user' => $this->user,
                'resolution' => $this->resolution,
                'resolutionUrl' => $this->resolutionUrl,
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

