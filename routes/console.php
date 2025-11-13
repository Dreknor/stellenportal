<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Models\LogEntry;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule job postings expiration check daily
Schedule::command('job-postings:mark-expired')->daily();

// Schedule notification for expiring job postings (7 days before expiration)
Schedule::command('job-postings:notify-expiring --days=7')->daily();

// Clean up old log entries from database (older than 30 days) daily at 3 AM
Schedule::call(function () {
    $days = config('logtodb.max_hours') ? config('logtodb.max_hours') / 24 : 30;
    $deleted = LogEntry::where('created_at', '<', now()->subDays($days))->delete();
    Log::info('Old log entries cleaned up', ['deleted' => $deleted, 'older_than_days' => $days]);
})->dailyAt('03:00')->name('cleanup-old-logs');

// Queue Worker Mode Configuration (set in .env: QUEUE_WORKER_MODE)
// - 'cronjob': Queue jobs are processed every minute via cronjob (default, simple setup)
// - 'supervisor': Queue worker runs continuously as daemon (recommended for production)
if (env('QUEUE_WORKER_MODE', 'cronjob') === 'cronjob') {
    Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute();
}
