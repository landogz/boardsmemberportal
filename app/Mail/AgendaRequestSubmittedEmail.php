<?php

namespace App\Mail;

use App\Models\AgendaInclusionRequest;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgendaRequestSubmittedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $agendaRequest;
    public $notice;
    public $user; // The user who submitted the request
    public $creator; // The notice creator
    public $agendaRequestUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(AgendaInclusionRequest $agendaRequest, Notice $notice, User $user, User $creator)
    {
        $this->agendaRequest = $agendaRequest;
        $this->notice = $notice;
        $this->user = $user;
        $this->creator = $creator;
        
        // Generate absolute URL to agenda request page based on creator's privilege
        $baseUrl = config('app.url');
        // Always redirect to admin agenda requests page for creators (they need to review it)
        $this->agendaRequestUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode('/admin/agenda-inclusion-requests/' . $agendaRequest->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Agenda Request: ' . $this->notice->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.agenda-request-submitted',
            with: [
                'agendaRequest' => $this->agendaRequest,
                'notice' => $this->notice,
                'user' => $this->user,
                'creator' => $this->creator,
                'agendaRequestUrl' => $this->agendaRequestUrl,
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

