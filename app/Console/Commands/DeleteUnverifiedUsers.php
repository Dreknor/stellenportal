<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-unverified {--days=5 : Number of days after which unverified users should be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users whose email has not been verified after the specified number of days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->info("Lösche nicht verifizierte Benutzerkonten, die älter als {$days} Tage sind...");

        // Only accounts that were never verified, are older than the threshold and
        // have not been assigned to an organization or facility yet
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays($days))
            ->whereDoesntHave('organizations')
            ->whereDoesntHave('facilities')
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('Keine nicht verifizierten Benutzerkonten zum Löschen gefunden.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($unverifiedUsers as $user) {
            Log::info('Deleting unverified user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'days_since_creation' => $user->created_at->diffInDays(now())
            ]);

            // Delete per model so that auditing and model events run
            $user->delete();
            $count++;
        }

        $this->info("Es wurden {$count} nicht verifizierte Benutzerkonten gelöscht.");

        return Command::SUCCESS;
    }
}
