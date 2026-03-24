<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAppointmentsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointments;
    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct($appointments, $recipientType = 'admin')
    {
        $this->appointments = $appointments;
        $this->recipientType = $recipientType; // 'admin' o 'doctor'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte Diario de Citas (' . now()->format('d/m/Y') . ') - MediMatch',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_appointments_report',
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
