<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $action;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, $action)
    {
        $this->appointment = $appointment;
        $this->action = $action;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Action Alert: Appointment ' . ucfirst($this->action))
                    ->view('emails.admin_notification');
    }
}
