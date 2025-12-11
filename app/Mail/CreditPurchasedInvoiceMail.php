<?php

namespace App\Mail;

use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreditPurchasedInvoiceMail extends Mailable implements ShouldQueue
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
     * Handle a job failure due to missing models.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure for debugging
        Log::error('CreditPurchasedInvoiceMail failed: ' . $exception->getMessage(), [
            'transaction_id' => $this->transaction?->id ?? 'unknown',
            'user_id' => $this->user?->id ?? 'unknown',
            'package_id' => $this->package?->id ?? 'unknown',
        ]);
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

