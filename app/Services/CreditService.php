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
use App\Exceptions\InsufficientCreditsException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CreditService
{
    /**
     * Lädt (oder erstellt) das CreditBalance-Row für ein Creditable und sperrt es
     * innerhalb der aktuellen DB-Transaktion (pessimistisches Locking), damit
     * nebenläufige Credit-Mutationen (publish/verbrauchen/transfer) nicht zu
     * Race Conditions führen.
     *
     * WICHTIG: Nur innerhalb einer laufenden DB::transaction aufrufen.
     */
    protected function lockBalance($creditable): CreditBalance
    {
        // Sicherstellen, dass ein Balance-Row existiert (außerhalb des Locks,
        // damit firstOrCreate unter Lock nicht neu anlegen muss).
        $creditable->creditBalance()->firstOrCreate([]);

        /** @var CreditBalance $balance */
        $balance = $creditable->creditBalance()
            ->lockForUpdate()
            ->firstOrFail();

        return $balance;
    }

    /**
     * Purchase credits for an entity (Organization or Facility)
     */
    public function purchaseCredits($creditable, CreditPackage $package, User $user, ?string $note = null)
    {
        // Check if package can still be purchased by this organization
        if (!$package->canBePurchasedBy($creditable)) {
            throw new \Exception('Dieses Paket kann nicht mehr gekauft werden. Das Kauflimit von ' . $package->purchase_limit_per_organization . ' wurde bereits erreicht.');
        }

        return DB::transaction(function () use ($creditable, $package, $user, $note) {
            // Balance mit Lock laden
            $balance = $this->lockBalance($creditable);

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
                'expires_at' => now()->addYears(CreditTransaction::EXPIRATION_YEARS),
            ]);

            $balance->balance += $package->credits;
            $balance->save();

            $this->sendPurchaseEmails($creditable, $transaction, $user, $package);

            return $transaction;
        });
    }

    /**
     * Transfer credits from organization to facility
     */
    public function transferCredits(Organization $organization, Facility $facility, int $amount, User $user, ?string $note = null)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Betrag muss größer als 0 sein.');
        }

        if ($facility->organization_id !== $organization->id) {
            throw new \Exception('Facility does not belong to this organization');
        }

        return DB::transaction(function () use ($organization, $facility, $amount, $user, $note) {
            // Deterministische Lock-Reihenfolge: Organization zuerst
            $orgBalance = $this->lockBalance($organization);
            $facilityBalance = $this->lockBalance($facility);

            if ($orgBalance->balance < $amount) {
                throw new InsufficientCreditsException();
            }

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

            $transferOut->related_transaction_id = $transferIn->id;
            $transferOut->save();

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
     * Transfer credits from facility back to organization
     */
    public function transferCreditsToOrganization(Facility $facility, int $amount, User $user, ?string $note = null)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Betrag muss größer als 0 sein.');
        }

        $organization = $facility->organization;

        if (!$organization) {
            throw new \Exception('Facility does not belong to any organization');
        }

        return DB::transaction(function () use ($facility, $organization, $amount, $user, $note) {
            // Deterministische Lock-Reihenfolge: Organization zuerst (siehe transferCredits)
            $orgBalance = $this->lockBalance($organization);
            $facilityBalance = $this->lockBalance($facility);

            if ($facilityBalance->balance < $amount) {
                throw new InsufficientCreditsException();
            }

            $transferOut = CreditTransaction::create([
                'creditable_id' => $facility->id,
                'creditable_type' => Facility::class,
                'user_id' => $user->id,
                'type' => CreditTransaction::TYPE_TRANSFER_OUT,
                'amount' => -$amount,
                'balance_after' => $facilityBalance->balance - $amount,
                'note' => $note,
                'related_creditable_id' => $organization->id,
                'related_creditable_type' => Organization::class,
            ]);

            $transferIn = CreditTransaction::create([
                'creditable_id' => $organization->id,
                'creditable_type' => Organization::class,
                'user_id' => $user->id,
                'type' => CreditTransaction::TYPE_TRANSFER_IN,
                'amount' => $amount,
                'balance_after' => $orgBalance->balance + $amount,
                'note' => $note,
                'related_creditable_id' => $facility->id,
                'related_creditable_type' => Facility::class,
                'related_transaction_id' => $transferOut->id,
            ]);

            $transferOut->related_transaction_id = $transferIn->id;
            $transferOut->save();

            $facilityBalance->balance -= $amount;
            $facilityBalance->save();

            $orgBalance->balance += $amount;
            $orgBalance->save();

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
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Betrag muss größer als 0 sein.');
        }

        return DB::transaction(function () use ($creditable, $amount, $user, $note) {
            // ATOMARES Check-Then-Write unter Lock.
            $balance = $this->lockBalance($creditable);

            if ($balance->balance < $amount) {
                throw new InsufficientCreditsException();
            }

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
            $balance = $this->lockBalance($creditable);

            // Auch Admin-Korrekturen dürfen keinen negativen Stand erzeugen.
            if (($balance->balance + $amount) < 0) {
                throw new InsufficientCreditsException(
                    'Die Korrektur würde einen negativen Kontostand erzeugen.'
                );
            }

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
     * Delegiert an adjustCredits und setzt bei Bedarf eine Standard-Notiz.
     *
     * @param mixed $creditable Organization|Facility
     */
    public function addCredits($creditable, int $amount, User $user, ?string $note = null)
    {
        $note = $note ?? 'Admin-Gutschrift';

        return $this->adjustCredits($creditable, $amount, $user, $note);
    }

    /**
     * Send purchase emails
     */
    protected function sendPurchaseEmails($creditable, CreditTransaction $transaction, User $user, CreditPackage $package)
    {
        $invoiceEmail = config('mail.invoice_email', config('mail.from.address'));
        Mail::to($invoiceEmail)->queue(new CreditPurchasedInvoiceMail($creditable, $transaction, $user, $package));

        Mail::to($user->email)->queue(new CreditPurchasedConfirmationMail($creditable, $transaction, $user, $package));
    }

    /**
     * Process expired credits
     * Returns the number of credits expired
     */
    public function processExpiredCredits(): int
    {
        $totalExpired = 0;

        $expiredPurchases = CreditTransaction::expiredPurchases()->get();

        foreach ($expiredPurchases as $purchase) {
            try {
                DB::transaction(function () use ($purchase, &$totalExpired) {
                    $creditable = $purchase->creditable;
                    if (!$creditable) {
                        return;
                    }

                    // Balance mit Lock laden (Race-frei)
                    $balance = $this->lockBalance($creditable);

                    $availableAmount = min($purchase->amount, $balance->balance);

                    if ($availableAmount > 0) {
                        CreditTransaction::create([
                            'creditable_id' => $creditable->id,
                            'creditable_type' => get_class($creditable),
                            'user_id' => $purchase->user_id,
                            'type' => CreditTransaction::TYPE_EXPIRATION,
                            'amount' => -$availableAmount,
                            'balance_after' => $balance->balance - $availableAmount,
                            'note' => 'Automatischer Verfall von Credits (gekauft am ' . $purchase->created_at->format('d.m.Y') . ', abgelaufen am ' . $purchase->expires_at->format('d.m.Y') . ')',
                            'related_transaction_id' => $purchase->id,
                        ]);

                        $balance->balance -= $availableAmount;
                        $balance->save();

                        $totalExpired += $availableAmount;
                    }
                });
            } catch (\Exception $e) {
                Log::error('Fehler beim Verarbeiten abgelaufener Credits', [
                    'transaction_id' => $purchase->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $totalExpired;
    }
}

