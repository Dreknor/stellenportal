<?php

namespace App\Observers;

use App\Models\LogEntry;
use App\Mail\CriticalLogNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class LogEntryObserver
{
    /**
     * Handle the LogEntry "created" event.
     */
    public function created(LogEntry $logEntry): void
    {
        // Get critical levels from config
        $criticalLevels = config('critical-logs.critical_levels', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']);

        // Only send notifications for critical log levels
        if (!in_array($logEntry->level_name, $criticalLevels)) {
            return;
        }

        // Don't send notifications if MAIL_SUPPORT_ADDRESS is not configured
        $supportEmail = config('critical-logs.support_address');
        if (empty($supportEmail)) {
            return;
        }

        // Get rate limiting settings
        $rateLimit = config('critical-logs.rate_limit_per_hour', 10);
        $dedupeMinutes = config('critical-logs.dedupe_minutes', 5);

        // Rate limiting: Don't send more than X critical log emails per hour
        $cacheKey = 'critical_log_notifications_' . now()->format('YmdH');
        $sentCount = Cache::get($cacheKey, 0);

        if ($sentCount >= $rateLimit) {
            // Log that we're skipping notification due to rate limiting
            \Log::channel('single')->warning('Critical log email notification skipped due to rate limiting', [
                'log_entry_id' => $logEntry->id,
                'sent_count' => $sentCount,
                'rate_limit' => $rateLimit,
            ]);
            return;
        }

        // Deduplicate: Don't send notification for identical messages within X minutes
        $dedupeKey = 'critical_log_dedupe_' . md5($logEntry->message . $logEntry->level_name);
        if (Cache::has($dedupeKey)) {
            return;
        }

        try {
            // Send email notification (queued or direct based on config)
            if (config('critical-logs.queue_notifications', true)) {
                Mail::to($supportEmail)->queue(new CriticalLogNotificationMail($logEntry));
            } else {
                Mail::to($supportEmail)->send(new CriticalLogNotificationMail($logEntry));
            }

            // Update rate limiting counter
            Cache::put($cacheKey, $sentCount + 1, now()->addHour());

            // Set deduplication flag
            Cache::put($dedupeKey, true, now()->addMinutes($dedupeMinutes));

            // Log the notification
            \Log::channel('single')->info('Critical log email notification sent', [
                'log_entry_id' => $logEntry->id,
                'level' => $logEntry->level_name,
                'support_email' => $supportEmail,
                'queued' => config('critical-logs.queue_notifications', true),
            ]);
        } catch (\Exception $e) {
            // Don't fail the log entry creation if email fails
            \Log::channel('single')->error('Failed to send critical log notification email', [
                'log_entry_id' => $logEntry->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

