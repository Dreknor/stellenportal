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
    protected $signature = 'users:delete-unverified {--days=7 : Number of days after which unverified users should be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user accounts that have not been verified within the specified time period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->info("Lösche nicht verifizierte Benutzerkonten, die älter als {$days} Tage sind...");

        // Find users who:
        // 1. Have not verified their email (email_verified_at is null)
        // 2. Were created more than X days ago
        $cutoffDate = now()->subDays($days);

        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        $count = $unverifiedUsers->count();

        if ($count === 0) {
            $this->info('Keine nicht verifizierten Benutzerkonten zum Löschen gefunden.');
            return Command::SUCCESS;
        }

        // Log the users that will be deleted
        foreach ($unverifiedUsers as $user) {
            Log::info('Deleting unverified user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'days_since_creation' => $user->created_at->diffInDays(now())
            ]);
        }

        // Delete the users
        User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Es wurden {$count} nicht verifizierte Benutzerkonten gelöscht.");

        return Command::SUCCESS;
    }
}

