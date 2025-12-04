<?php

namespace App\Console\Commands;

use App\Services\CreditService;
use Illuminate\Console\Command;

class ExpireCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verarbeite abgelaufene Credits (3 Jahre nach Kauf)';

    /**
     * Execute the console command.
     */
    public function handle(CreditService $service): int
    {
        $this->info('Verarbeite abgelaufene Credits...');

        $expiredCount = $service->processExpiredCredits();

        if ($expiredCount > 0) {
            $this->info("Es wurden {$expiredCount} Credits als abgelaufen markiert und vom Guthaben abgezogen.");
        } else {
            $this->info('Keine abgelaufenen Credits gefunden.');
        }

        return Command::SUCCESS;
    }
}
