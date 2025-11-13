<?php

namespace App\Mail;

use App\Models\LogEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CriticalLogNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LogEntry $log
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->log->level_name) {
            'EMERGENCY' => '🚨 NOTFALL',
            'ALERT' => '🔴 ALARM',
            'CRITICAL' => '⚠️ KRITISCH',
            'ERROR' => '❌ FEHLER',
            default => '⚠️ WARNUNG',
        };

        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: "[{$subject}] " . config('app.name') . ' - ' . Str::limit($this->log->message, 50),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.critical-log-notification',
            text: 'emails.critical-log-notification-text',
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

