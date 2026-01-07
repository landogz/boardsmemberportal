<?php

namespace App\Mail;

use App\Models\AgendaInclusionRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgendaRequestRejectedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $agendaRequest;
    public $user;
    public $noticeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(AgendaInclusionRequest $agendaRequest, User $user)
    {
        $this->agendaRequest = $agendaRequest;
        $this->user = $user;
        // Generate absolute URL to login page with redirect to notice
        $baseUrl = config('app.url');
        $redirectPath = in_array($user->privilege, ['admin', 'consec']) 
            ? '/admin/notices/' . $agendaRequest->notice_id 
            : '/notices/' . $agendaRequest->notice_id;
        $this->noticeUrl = rtrim($baseUrl, '/') . '/login?redirect=' . urlencode($redirectPath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Agenda Request Rejected: ' . $this->agendaRequest->notice->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.agenda-request-rejected',
            with: [
                'agendaRequest' => $this->agendaRequest,
                'user' => $this->user,
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
