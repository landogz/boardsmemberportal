<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DisapprovedRegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $userEmail;
    public $rejectionReason;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $userEmail, $rejectionReason = null)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->rejectionReason = $rejectionReason ?? 'No reason provided';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Not Approved - Board Members Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-disapproved',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'rejectionReason' => $this->rejectionReason,
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

