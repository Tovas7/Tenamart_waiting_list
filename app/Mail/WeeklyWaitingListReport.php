<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyWaitingListReport extends Mailable
{
    use Queueable, SerializesModels;

    public $stats; // Public property to pass data to the view

    /**
     * Create a new message instance.
     *
     * @param array $stats The statistics data to include in the report.
     * @return void
     */
    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly TenaMart Waiting List Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.waiting-list.report', // Points to resources/views/emails/waiting-list/report.blade.php
            with: [
                'stats' => $this->stats, // Pass the stats data to the view
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
