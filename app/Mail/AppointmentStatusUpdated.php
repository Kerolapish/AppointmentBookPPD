<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment; // Added this
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon; // Added this

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
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->buildIcsContent($this->appointment), 'invite.ics')
                ->withMime('text/calendar'),
        ];
    }

    /**
     * Build the raw text for the .ics calendar invite.
     */
    private function buildIcsContent($appointment)
    {
        // 1. Format Dates (Must be UTC for ICS standard)
        $start = Carbon::parse($appointment->date . ' ' . $appointment->time)->setTimezone('UTC');
        $end = $start->copy()->addHour(); // Strict 1-hour slot based on your UI rules

        $dtStart = $start->format('Ymd\THis\Z');
        $dtEnd = $end->format('Ymd\THis\Z');
        $now = now()->setTimezone('UTC')->format('Ymd\THis\Z');

        // 2. Set Details
        $uid = uniqid() . '@ppdkluang.system'; // Unique ID for the event
        $summary = "Appointment: " . ($appointment->purpose ?? 'Consultation');
        $location = $appointment->location ?? 'Private Unit Office, PPD Kluang';

        // 3. Define the Guest Emails (UPDATE THESE FOR YOUR DEMO)
        $userEmail = $appointment->user->email ?? 'user@example.com';
        $adminEmail = 'admin@yourdomain.com'; // Change to actual admin email
        $superAdminEmail = 'superadmin@yourdomain.com'; // Change to actual super admin email

        // 4. Build the official iCal formatting array
        $ics = [
            "BEGIN:VCALENDAR",
            "VERSION:2.0",
            "PRODID:-//PPD Kluang Booking System//EN",
            "METHOD:REQUEST", // Tells Gmail this is a formal invite
            "BEGIN:VEVENT",
            "UID:{$uid}",
            "DTSTAMP:{$now}",
            "DTSTART:{$dtStart}",
            "DTEND:{$dtEnd}",
            "SUMMARY:{$summary}",
            "LOCATION:{$location}",
            "ORGANIZER;CN=PPD Kluang Admin:mailto:{$adminEmail}",
            "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$appointment->user->name}:mailto:{$userEmail}",
            "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=Admin:mailto:{$adminEmail}",
            "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=Super Admin:mailto:{$superAdminEmail}",
            "END:VEVENT",
            "END:VCALENDAR"
        ];

        // iCal standard dictates strict \r\n line breaks
        return implode("\r\n", $ics);
    }
}
