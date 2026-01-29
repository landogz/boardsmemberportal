<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingRegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $registeredUser;
    public $adminUser;
    public $pendingRegistrationsUrl;
    public $forRegistrant;

    /**
     * Create a new message instance.
     *
     * @param User $registeredUser The user who just registered
     * @param User|null $adminUser The admin recipient (when notifying admins). Pass same as registeredUser when forRegistrant=true
     * @param bool $forRegistrant When true, email is for the registering user (pending approval notice). When false, for admins (new registration alert)
     */
    public function __construct(User $registeredUser, ?User $adminUser = null, bool $forRegistrant = false)
    {
        $this->registeredUser = $registeredUser;
        $this->adminUser = $adminUser ?? $registeredUser;
        $this->forRegistrant = $forRegistrant;
        // Generate absolute URL to login page with redirect to pending registrations
        $baseUrl = config('app.url');
        $this->pendingRegistrationsUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode('/admin/pending-registrations');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->forRegistrant ? 'Registration Submitted - Pending Approval' : 'New Registration Pending Approval',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending-registration',
            with: [
                'registeredUser' => $this->registeredUser,
                'adminUser' => $this->adminUser,
                'pendingRegistrationsUrl' => $this->pendingRegistrationsUrl,
                'forRegistrant' => $this->forRegistrant,
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

