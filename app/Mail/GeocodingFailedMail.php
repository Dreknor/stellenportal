<?php

namespace App\Mail;

use App\Models\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeocodingFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  Address[]  $failedAddresses
     */
    public function __construct(
        public array $failedAddresses
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = count($this->failedAddresses);

        return new Envelope(
            subject: "⚠️ Geocodierung fehlgeschlagen: {$count} " . ($count === 1 ? 'Adresse' : 'Adressen') . ' ohne Koordinaten – ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.geocoding-failed',
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

