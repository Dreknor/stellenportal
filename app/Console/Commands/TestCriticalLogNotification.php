<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCriticalLogNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:test-notification {--level=error : Log level (error, critical, alert, emergency)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test critical log notification to verify email notifications are working';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $supportEmail = config('critical-logs.support_address');

        if (empty($supportEmail)) {
            $this->error('MAIL_SUPPORT_ADDRESS is not configured in .env file.');
            $this->line('Please set MAIL_SUPPORT_ADDRESS to receive critical log notifications.');
            return self::FAILURE;
        }

        $this->info("Testing critical log notification system...");
        $this->line("Support email: {$supportEmail}");

        $level = strtolower($this->option('level'));
        $validLevels = ['error', 'critical', 'alert', 'emergency'];

        if (!in_array($level, $validLevels)) {
            $this->error("Invalid log level. Must be one of: " . implode(', ', $validLevels));
            return self::FAILURE;
        }

        $this->line("Creating {$level} log entry...");

        $context = [
            'test' => true,
            'timestamp' => now()->toDateTimeString(),
            'user_id' => 999,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Command',
        ];

        // Create log based on level
        switch ($level) {
            case 'error':
                Log::error('Test ERROR log notification - This is a test error message', $context);
                break;
            case 'critical':
                Log::critical('Test CRITICAL log notification - Critical system error detected', $context);
                break;
            case 'alert':
                Log::alert('Test ALERT log notification - Action must be taken immediately', $context);
                break;
            case 'emergency':
                Log::emergency('Test EMERGENCY log notification - System is unusable', $context);
                break;
        }

        $this->newLine();
        $this->info("✓ Test {$level} log created successfully!");

        if (config('critical-logs.queue_notifications')) {
            $this->line("→ Email notification has been queued.");
            $this->line("→ Run 'php artisan queue:work --once' to process the job.");
        } else {
            $this->line("→ Email notification has been sent directly.");
        }

        $this->newLine();
        $this->line("Check your email at: {$supportEmail}");
        $this->line("Check the log database: SELECT * FROM log ORDER BY id DESC LIMIT 1;");

        $rateLimit = config('critical-logs.rate_limit_per_hour', 10);
        $this->newLine();
        $this->comment("Rate limiting: {$rateLimit} emails per hour");
        $this->comment("Deduplication: " . config('critical-logs.dedupe_minutes', 5) . " minutes");

        return self::SUCCESS;
    }
}

