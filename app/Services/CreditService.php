<?php

namespace App\Services;

use App\Models\CreditBalance;
use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;
use App\Mail\CreditPurchasedInvoiceMail;
use App\Mail\CreditPurchasedConfirmationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreditService
{
    /**
     * Purchase credits for an entity (Organization or Facility)
     */
    public function purchaseCredits($creditable, CreditPackage $package, User $user, ?string $note = null)
    {
        // Check if package can still be purchased by this organization
        if (!$package->canBePurchasedBy($creditable)) {
            $remaining = $package->getRemainingPurchasesFor($creditable);
            throw new \Exception('Dieses Paket kann nicht mehr gekauft werden. Das Kauflimit von ' . $package->purchase_limit_per_organization . ' wurde bereits erreicht.');
        }

        return DB::transaction(function () use ($creditable, $package, $user, $note) {
            // Get or create credit balance
            $balance = $creditable->creditBalance()->firstOrCreate([]);

            // Create transaction
            $transaction = CreditTransaction::create([
                'creditable_id' => $creditable->id,
                'creditable_type' => get_class($creditable),
                'user_id' => $user->id,
                'credit_package_id' => $package->id,
                'type' => CreditTransaction::TYPE_PURCHASE,
                'amount' => $package->credits,
                'balance_after' => $balance->balance + $package->credits,
                'price_paid' => $package->price,
                'note' => $note,
            ]);

            // Update balance
            $balance->balance += $package->credits;
            $balance->save();

            // Send emails
            $this->sendPurchaseEmails($creditable, $transaction, $user, $package);

            return $transaction;
        });
    }

    /**
     * Transfer credits from organization to facility
     */
    public function transferCredits(Organization $organization, Facility $facility, int $amount, User $user, ?string $note = null)
    {
        if ($facility->organization_id !== $organization->id) {
            throw new \Exception('Facility does not belong to this organization');
        }

        if ($organization->getCurrentCreditBalance() < $amount) {
            throw new \Exception('Insufficient credits');
        }

        return DB::transaction(function () use ($organization, $facility, $amount, $user, $note) {
            // Get balances
            $orgBalance = $organization->creditBalance()->firstOrCreate([]);
            $facilityBalance = $facility->creditBalance()->firstOrCreate([]);

            // Create transfer out transaction for organization
            $transferOut = CreditTransaction::create([
                'creditable_id' => $organization->id,
                'creditable_type' => Organization::class,
                'user_id' => $user->id,
                'type' => CreditTransaction::TYPE_TRANSFER_OUT,
                'amount' => -$amount,
                'balance_after' => $orgBalance->balance - $amount,
                'note' => $note,
                'related_creditable_id' => $facility->id,
                'related_creditable_type' => Facility::class,
            ]);

            // Create transfer in transaction for facility
            $transferIn = CreditTransaction::create([
                'creditable_id' => $facility->id,
                'creditable_type' => Facility::class,
                'user_id' => $user->id,
                'type' => CreditTransaction::TYPE_TRANSFER_IN,
                'amount' => $amount,
                'balance_after' => $facilityBalance->balance + $amount,
                'note' => $note,
                'related_creditable_id' => $organization->id,
                'related_creditable_type' => Organization::class,
                'related_transaction_id' => $transferOut->id,
            ]);

            // Link transactions
            $transferOut->related_transaction_id = $transferIn->id;
            $transferOut->save();

            // Update balances
            $orgBalance->balance -= $amount;
            $orgBalance->save();

            $facilityBalance->balance += $amount;
            $facilityBalance->save();

            return [
                'transfer_out' => $transferOut,
                'transfer_in' => $transferIn,
            ];
        });
    }

    /**
     * Use credits
     */
    public function useCredits($creditable, int $amount, User $user, ?string $note = null)
    {
        if ($creditable->getCurrentCreditBalance() < $amount) {
            throw new \Exception('Insufficient credits');
        }

        return DB::transaction(function () use ($creditable, $amount, $user, $note) {
            $balance = $creditable->creditBalance()->firstOrCreate([]);

            $transaction = CreditTransaction::create([
                'creditable_id' => $creditable->id,
                'creditable_type' => get_class($creditable),
                'user_id' => $user->id,
                'type' => CreditTransaction::TYPE_USAGE,
                'amount' => -$amount,
                'balance_after' => $balance->balance - $amount,
                'note' => $note,
            ]);

            $balance->balance -= $amount;
            $balance->save();

            return $transaction;
        });
    }

    /**
     * Adjust credits (for administrators)
     */
    public function adjustCredits($creditable, int $amount, User $user, string $note)
    {
        return DB::transaction(function () use ($creditable, $amount, $user, $note) {
            $balance = $creditable->creditBalance()->firstOrCreate([]);

            $transaction = CreditTransaction::create([
                'creditable_id' => $creditable->id,
                'creditable_type' => get_class($creditable),
                'user_id' => $user->id,
                'type' => CreditTransaction::TYPE_ADJUSTMENT,
                'amount' => $amount,
                'balance_after' => $balance->balance + $amount,
                'note' => $note,
            ]);

            $balance->balance += $amount;
            $balance->save();

            return $transaction;
        });
    }

    /**
     * Add credits (admin convenience wrapper)
     *
     * Kehrt sich nicht von adjustCredits ab, sondern nutzt diese Methode.
     * Signatur entspricht der Verwendung in `Admin\\CreditController`.
     *
     * @param mixed $creditable Organization|Facility
     * @param int $amount
     * @param User $user
     * @param string|null $note
     * @return CreditTransaction
     */
    public function addCredits($creditable, int $amount, User $user, ?string $note = null)
    {
        // Verwende adjustCredits und setze einen Standard-Note-Text, falls nicht angegeben.
        $note = $note ?? 'Admin-Gutschrift';

        return $this->adjustCredits($creditable, $amount, $user, $note);
    }

    /**
     * Send purchase emails
     */
    protected function sendPurchaseEmails($creditable, CreditTransaction $transaction, User $user, CreditPackage $package)
    {
        // Email an Rechnungssteller (z.B. Buchhaltung)
        $invoiceEmail = config('mail.invoice_email', config('mail.from.address'));
        Mail::to($invoiceEmail)->queue(new CreditPurchasedInvoiceMail($creditable, $transaction, $user, $package));

        // Bestätigungs-Email an den Käufer
        Mail::to($user->email)->queue(new CreditPurchasedConfirmationMail($creditable, $transaction, $user, $package));
    }
}

