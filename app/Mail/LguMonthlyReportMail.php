<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LguMonthlyReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $periodLabel,
        public string $pdfPath,
        public array $stats
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SK Namayan LGU Monthly Report — ' . $this->periodLabel,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.lgu-monthly-report',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as(basename($this->pdfPath))
                ->withMime('application/pdf'),
        ];
    }
}
