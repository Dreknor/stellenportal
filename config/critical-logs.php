<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Support Email Address
    |--------------------------------------------------------------------------
    |
    | This email address will receive critical log notifications.
    | Set to null or empty string to disable notifications.
    |
    */

    'support_address' => env('MAIL_SUPPORT_ADDRESS'),

    /*
    |--------------------------------------------------------------------------
    | Critical Log Levels
    |--------------------------------------------------------------------------
    |
    | Log levels that will trigger email notifications.
    | Available levels: DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY
    |
    */

    'critical_levels' => [
        'ERROR',
        'CRITICAL',
        'ALERT',
        'EMERGENCY',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum number of critical log emails to send per hour.
    | This prevents email spam in case of cascading errors.
    |
    */

    'rate_limit_per_hour' => env('CRITICAL_LOG_RATE_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | Deduplication Window
    |--------------------------------------------------------------------------
    |
    | Time window (in minutes) to deduplicate identical log messages.
    | Identical messages within this window will only trigger one notification.
    |
    */

    'dedupe_minutes' => env('CRITICAL_LOG_DEDUPE_MINUTES', 5),

    /*
    |--------------------------------------------------------------------------
    | Queue Notifications
    |--------------------------------------------------------------------------
    |
    | Whether to queue critical log email notifications.
    | Recommended: true (to prevent slowing down the application)
    |
    */

    'queue_notifications' => env('CRITICAL_LOG_QUEUE', true),

];

