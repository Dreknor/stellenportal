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

