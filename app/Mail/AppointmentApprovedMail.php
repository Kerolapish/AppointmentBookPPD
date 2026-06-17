<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Carbon\Carbon;

class AppointmentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct($appointment)
    {
        // Pass the appointment model instance to the mail view
        $this->appointment = $appointment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Confirmed - PPD Kluang',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment_approved',
        );
    }

    /**
     * Get the attachments for the message.
     * Generates the Google Calendar .ics invite file automatically.
     */
    public function attachments(): array
    {
        // Format times for ICS (Assuming your app runs in Asia/Kuala_Lumpur)
        $startTime = Carbon::parse($this->appointment->date . ' ' . $this->appointment->time)
                        ->setTimezone('Asia/Kuala_Lumpur')
                        ->format('Ymd\THis');
                        
        $endTime = Carbon::parse($this->appointment->date . ' ' . $this->appointment->time)
                        ->addHour() // Assumes a 1 hour appointment
                        ->setTimezone('Asia/Kuala_Lumpur')
                        ->format('Ymd\THis');
                        
        $now = Carbon::now()->setTimezone('UTC')->format('Ymd\THis\Z');

        // IMPORTANT: Change this to your real demo admin email
        $organizerEmail = 'orokanateki@gmail.com'; 
        
        $userEmail = $this->appointment->user->email ?? 'no-reply@ppdkluang.com';
        $userName = $this->appointment->user->name ?? 'User';

        $icsContent = "BEGIN:VCALENDAR\n" .
            "VERSION:2.0\n" .
            "PRODID:-//PPD Kluang//Appointment System//EN\n" .
            "METHOD:REQUEST\n" .
            "BEGIN:VEVENT\n" .
            "UID:" . uniqid() . "@ppdkluang.com\n" .
            "DTSTAMP:" . $now . "\n" .
            "DTSTART;TZID=Asia/Kuala_Lumpur:" . $startTime . "\n" .
            "DTEND;TZID=Asia/Kuala_Lumpur:" . $endTime . "\n" .
            "SUMMARY:Appointment Confirmation: " . $this->appointment->purpose . "\n" .
            "DESCRIPTION:Location: " . $this->appointment->location . "\n" .
            "LOCATION:" . $this->appointment->location . "\n" .
            "STATUS:CONFIRMED\n" .
            "ORGANIZER;CN=PPD Kluang Admin:mailto:" . $organizerEmail . "\n" .
            "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=" . $userName . ":mailto:" . $userEmail . "\n" .
            "END:VEVENT\n" .
            "END:VCALENDAR";

        return [
            Attachment::fromData(fn () => $icsContent, 'invite.ics')
                ->withMime('text/calendar'),
        ];
    }
}