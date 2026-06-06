<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $messageBody; // 👈 Add this property to store the text message

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, $messageBody)
    {
        $this->appointment = $appointment;
        $this->messageBody = $messageBody; // 👈 Assign the message body here
    }

    /**
     * Get the message envelope.
     */
    public function __envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Update Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function __content(): Content
    {
        return new Content(
            view: 'emails.appointment_status',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}