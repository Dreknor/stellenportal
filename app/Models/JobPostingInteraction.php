<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPostingInteraction extends Model
{
    const UPDATED_AT = null; // We only need created_at

    protected $fillable = [
        'job_posting_id',
        'interaction_type',
        'ip_address',
        'user_agent',
        'session_id',
    ];

    // Interaction types
    const TYPE_VIEW = 'view';
    const TYPE_APPLY_CLICK = 'apply_click';
    const TYPE_EMAIL_REVEAL = 'email_reveal';
    const TYPE_PHONE_REVEAL = 'phone_reveal';
    const TYPE_DOWNLOAD = 'download';

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Track an interaction (avoid duplicates within same session)
     */
    public static function track(int $jobPostingId, string $type, ?string $sessionId = null): void
    {
        // Check if this interaction was already tracked in this session
        if ($sessionId) {
            $exists = self::where('job_posting_id', $jobPostingId)
                ->where('interaction_type', $type)
                ->where('session_id', $sessionId)
                ->where('created_at', '>', now()->subMinutes(30)) // Within last 30 minutes
                ->exists();

            if ($exists) {
                return; // Already tracked
            }
        }

        self::create([
            'job_posting_id' => $jobPostingId,
            'interaction_type' => $type,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => $sessionId,
        ]);
    }
}


