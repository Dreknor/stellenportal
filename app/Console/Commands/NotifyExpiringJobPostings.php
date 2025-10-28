<?php

namespace App\Console\Commands;

use App\Services\JobPostingService;
use Illuminate\Console\Command;

class NotifyExpiringJobPostings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job-postings:notify-expiring {--days=7 : Number of days before expiration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify creators about job postings that are expiring soon';

    /**
     * Execute the console command.
     */
    public function handle(JobPostingService $service): int
    {
        $days = (int) $this->option('days');

        $this->info("Sende Benachrichtigungen für Stellenausschreibungen, die in {$days} Tagen ablaufen...");

        $count = $service->notifyExpiringPostings($days);

        $this->info("Es wurden {$count} Benachrichtigungen versendet.");

        return Command::SUCCESS;
    }
}

