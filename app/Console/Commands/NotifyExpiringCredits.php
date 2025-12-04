<?php

namespace App\Console\Commands;

use App\Mail\CreditsExpiringMail;
use App\Models\CreditTransaction;
use App\Models\Organization;
use App\Models\Facility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyExpiringCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:notify-expiring {--days=30 : Anzahl der Tage bis zum Ablauf}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Benachrichtige Organisationen und Einrichtungen über ablaufende Credits';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Suche nach Credits, die in {$days} Tagen ablaufen...");

        $expirationDate = now()->addDays($days);
        $notificationsSent = 0;

        // Finde alle Kauftransaktionen, die bald ablaufen
        $expiringTransactions = CreditTransaction::where('type', CreditTransaction::TYPE_PURCHASE)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '=', $expirationDate->toDateString())
            ->whereDoesntHave('expirationTransaction')
            ->get();

        // Gruppiere nach creditable (Organization oder Facility)
        $groupedByCreditable = $expiringTransactions->groupBy(function ($transaction) {
            return $transaction->creditable_type . '_' . $transaction->creditable_id;
        });

        foreach ($groupedByCreditable as $group) {
            $firstTransaction = $group->first();
            $creditable = $firstTransaction->creditable;

            if (!$creditable) {
                continue;
            }

            $totalExpiringCredits = $group->sum('amount');

            // Finde primären Kontakt für Benachrichtigungen
            $recipients = $this->getRecipientsForCreditable($creditable);

            if (empty($recipients)) {
                $this->warn("Keine Empfänger für {$creditable->name} gefunden.");
                continue;
            }

            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->queue(
                        new CreditsExpiringMail(
                            $creditable,
                            $group->toArray(),
                            $totalExpiringCredits,
                            $days
                        )
                    );
                    $notificationsSent++;
                } catch (\Exception $e) {
                    $this->error("Fehler beim Senden der E-Mail an {$recipient->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Es wurden {$notificationsSent} Benachrichtigungen versendet.");

        return Command::SUCCESS;
    }

    /**
     * Get recipients for notifications based on creditable type
     */
    protected function getRecipientsForCreditable($creditable): array
    {
        if ($creditable instanceof Organization) {
            // Finde Admin-Benutzer der Organisation
            return $creditable->users()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'organization_admin');
                })
                ->get()
                ->toArray();
        }

        if ($creditable instanceof Facility) {
            // Finde Admin-Benutzer der Einrichtung
            $facilityUsers = $creditable->users()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'facility_admin');
                })
                ->get();

            // Wenn keine Facility-Admins, verwende Org-Admins
            if ($facilityUsers->isEmpty() && $creditable->organization) {
                return $creditable->organization->users()
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'organization_admin');
                    })
                    ->get()
                    ->toArray();
            }

            return $facilityUsers->toArray();
        }

        return [];
    }
}

