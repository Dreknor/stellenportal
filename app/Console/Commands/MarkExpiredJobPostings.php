<?php

namespace App\Console\Commands;

use App\Services\JobPostingService;
use Illuminate\Console\Command;

class MarkExpiredJobPostings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job-postings:mark-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired job postings';

    /**
     * Execute the console command.
     */
    public function handle(JobPostingService $service): int
    {
        $this->info('Markiere abgelaufene Stellenausschreibungen...');

        $count = $service->markExpiredPostings();

        $this->info("Es wurden {$count} Stellenausschreibungen als abgelaufen markiert.");

        return Command::SUCCESS;
    }
}
