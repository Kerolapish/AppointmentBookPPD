<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AppointmentApprovedMail_New extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message using the direct attachData approach.
     * This bypasses the Attachment::fromData abstraction layer
     * for maximum compatibility with all server environments.
     */
    public function build()
    {
        \Log::info('[DEBUG-ICS] AppointmentApprovedMail_New::build() WAS CALLED');

        $icsContent = $this->buildIcsContent($this->appointment);

        \Log::info('[DEBUG-ICS] ICS content length: ' . strlen($icsContent));

        $result = $this->subject('Appointment Confirmed - PPD Kluang')
                    ->view('emails.appointment_approved')
                    ->attachData($icsContent, 'invite.ics', [
                        'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
                    ]);

        \Log::info('[DEBUG-ICS] rawAttachments count after attachData: ' . count($this->rawAttachments));

        return $result;
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
        $adminEmail = 'mkhairulhaf@gmail.com'; // Change to actual admin email

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
            "END:VEVENT",
            "END:VCALENDAR"
        ];

        // iCal standard dictates strict \r\n line breaks
        return implode("\r\n", $ics);
    }
}
