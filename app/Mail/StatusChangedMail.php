<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $requestModel;
    public $referenceNumber;
    public $typeLabel;

    /**
     * Create a new message instance.
     */
    public function __construct($requestModel)
    {
        $this->requestModel = $requestModel;

        // Kinukuha ang reference number o gumagawa ng default format
        $this->referenceNumber = $requestModel->reference_number ?? ('SK-REQ-' . str_pad($requestModel->id, 5, '0', STR_PAD_LEFT));

        // Ginawang static 'General Request' dahil inalis na ang mga specific models
        $this->typeLabel = 'General Request';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SK Portal Request Status Changed - ' . $this->referenceNumber,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.status-changed',
        );
    }
}
