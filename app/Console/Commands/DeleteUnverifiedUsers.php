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


        // Find users who:
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('Keine unverifizierte Benutzer zum Löschen gefunden.');
            return Command::SUCCESS;
        }

        $count = $unverifiedUsers->count();

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
            ->delete();


        return Command::SUCCESS;
    }
}

