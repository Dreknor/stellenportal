<?php

namespace App\Mail;

use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreditPurchasedInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $creditable;
    public $transaction;
    public $user;
    public $package;

    /**
     * Create a new message instance.
     */
    public function __construct($creditable, CreditTransaction $transaction, User $user, CreditPackage $package)
    {
        $this->creditable = $creditable;
        $this->transaction = $transaction;
        $this->user = $user;
        $this->package = $package;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Neue Guthaben-Bestellung - Rechnung erforderlich',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.credit-purchased-invoice',
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

