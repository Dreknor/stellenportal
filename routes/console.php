<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule job postings expiration check daily
Schedule::command('job-postings:mark-expired')->daily();

// Schedule notification for expiring job postings (7 days before expiration)
Schedule::command('job-postings:notify-expiring --days=7')->daily();

// Schedule deletion of unverified users after 7 days
Schedule::command('users:delete-unverified --days=7')->daily();

// Queue Worker Mode Configuration (set in .env: QUEUE_WORKER_MODE)
// - 'cronjob': Queue jobs are processed every minute via cronjob (default, simple setup)
// - 'supervisor': Queue worker runs continuously as daemon (recommended for production)
if (env('QUEUE_WORKER_MODE', 'cronjob') === 'cronjob') {
    Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute();
}
