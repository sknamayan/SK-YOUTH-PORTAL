<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KkProfileSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $profile;

    /**
     * Create a new message instance.
     */
    public function __construct($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SK Portal - Katipunan ng Kabataan Profile Received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.kk-profile-submitted',
        );
    }
}
