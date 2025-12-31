<?php

namespace App\Mail;

use App\Models\BoardRegulation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoardRegulationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $regulation;
    public $regulationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, BoardRegulation $regulation)
    {
        $this->user = $user;
        $this->regulation = $regulation;
        
        // Generate absolute URL to login page with redirect
        // For regular users: /referendums
        // For consec: /admin/notifications
        $baseUrl = config('app.url');
        $regulationPath = ($user->privilege === 'consec' || $user->privilege === 'admin') 
            ? '/admin/notifications' 
            : '/referendums';
        $this->regulationUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($regulationPath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Board Regulation Published - Board Members Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.board-regulation',
            with: [
                'user' => $this->user,
                'regulation' => $this->regulation,
                'regulationUrl' => $this->regulationUrl,
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

