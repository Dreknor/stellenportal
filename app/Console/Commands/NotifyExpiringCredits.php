<?php

namespace App\Console\Commands;

use App\Mail\CreditsExpiringMail;
use App\Models\CreditLot;
use App\Models\Organization;
use App\Models\Facility;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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

        // Nur der noch nicht verbrauchte Rest einer Charge kann verfallen
        $expiringLots = CreditLot::where('remaining', '>', 0)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '=', $expirationDate->toDateString())
            ->with('purchaseTransaction')
            ->get();

        // Gruppiere nach creditable (Organization oder Facility)
        $groupedByCreditable = $expiringLots->groupBy(function (CreditLot $lot) {
            return $lot->creditable_type . '_' . $lot->creditable_id;
        });

        foreach ($groupedByCreditable as $group) {
            $creditable = $group->first()->creditable;

            if (!$creditable) {
                continue;
            }

            $totalExpiringCredits = (int) $group->sum('remaining');
            $details = $group->map(fn (CreditLot $lot) => [
                'purchased_at' => ($lot->purchaseTransaction?->created_at ?? $lot->created_at)->format('d.m.Y'),
                'amount' => $lot->remaining,
                'expires_at' => $lot->expires_at->format('d.m.Y'),
            ])->values()->all();

            $recipients = $this->getRecipientsForCreditable($creditable);

            if ($recipients->isEmpty()) {
                $this->warn("Keine Empfänger für {$creditable->name} gefunden.");
                continue;
            }

            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->queue(
                        new CreditsExpiringMail(
                            $creditable,
                            $details,
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
     * Alle Benutzer der Organisation bzw. der Einrichtung und ihrer Organisation
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    protected function getRecipientsForCreditable(Organization|Facility $creditable): Collection
    {
        $users = $creditable->users()->get();

        if ($creditable instanceof Facility && $creditable->organization) {
            $users = $users->merge($creditable->organization->users()->get());
        }

        return $users->unique('id')->filter(fn ($user) => !empty($user->email))->values();
    }
}
