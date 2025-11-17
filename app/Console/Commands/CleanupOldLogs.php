<?php

namespace App\Console\Commands;

use App\Models\LogEntry;
use Illuminate\Console\Command;

class CleanupOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old log entries from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days < 1) {
            $this->error('Days must be a positive integer.');
            return self::FAILURE;
        }

        $this->info("Deleting log entries older than {$days} days...");

        $date = now()->subDays($days);
        $count = LogEntry::where('created_at', '<', $date)->count();

        if ($count === 0) {
            $this->info('No old log entries found.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Found {$count} log entries. Do you want to delete them?", true)) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $deleted = LogEntry::where('created_at', '<', $date)->delete();

        $this->info("Successfully deleted {$deleted} log entries.");

        return self::SUCCESS;
    }
}

