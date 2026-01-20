<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class JobPosting extends Model implements Auditable
{
    use HasFactory;
    use HasSlug, SoftDeletes , \OwenIt\Auditing\Auditable;

    const EMPLOYMENT_TYPE_FULL_TIME = 'full_time';
    const EMPLOYMENT_TYPE_PART_TIME = 'part_time';
    const EMPLOYMENT_TYPE_MINI_JOB = 'mini_job';
    const EMPLOYMENT_TYPE_INTERNSHIP = 'internship';
    const EMPLOYMENT_TYPE_APPRENTICESHIP = 'apprenticeship';
    const EMPLOYMENT_TYPE_VOLUNTEER = 'volunteer';

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PAUSED = 'paused';

    const CREDITS_PER_POSTING = 1;
    const POSTING_DURATION_MONTHS = 3;

    protected $fillable = [
        'facility_id',
        'created_by',
        'title',
        'slug',
        'description',
        'employment_type',
        'job_category',
        'requirements',
        'benefits',
        'seo_keywords',
        'contact_email',
        'contact_phone',
        'contact_person',
        'status',
        'published_at',
        'expires_at',
        'credits_used',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interactions()
    {
        return $this->hasMany(JobPostingInteraction::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Check if the job posting is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->published_at
            && $this->published_at->isPast()
            && $this->expires_at
            && $this->expires_at->isFuture();
    }

    /**
     * Check if the job posting is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the job posting is a draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Get employment type label
     */
    public function getEmploymentTypeLabel(): string
    {
        return match($this->employment_type) {
            self::EMPLOYMENT_TYPE_FULL_TIME => 'Vollzeit',
            self::EMPLOYMENT_TYPE_PART_TIME => 'Teilzeit',
            self::EMPLOYMENT_TYPE_MINI_JOB => 'Minijob',
            self::EMPLOYMENT_TYPE_INTERNSHIP => 'Praktikum',
            self::EMPLOYMENT_TYPE_APPRENTICESHIP => 'Ausbildung',
            self::EMPLOYMENT_TYPE_VOLUNTEER => 'Ehrenamt',
            default => 'Unbekannt',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Entwurf',
            self::STATUS_ACTIVE => 'Aktiv',
            self::STATUS_EXPIRED => 'Abgelaufen',
            self::STATUS_PAUSED => 'Pausiert',
            default => 'Unbekannt',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_ACTIVE => 'green',
            self::STATUS_EXPIRED => 'red',
            self::STATUS_PAUSED => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Scope to get active postings
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to get drafts
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope to get expired postings
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Get the address through the facility
     */
    public function getAddressAttribute()
    {
        return $this->facility->address;
    }

    /**
     * Get interaction statistics
     */
    public function getInteractionStats(): array
    {
        $stats = $this->interactions()
            ->selectRaw('interaction_type, COUNT(*) as count')
            ->groupBy('interaction_type')
            ->pluck('count', 'interaction_type')
            ->toArray();

        return [
            'views' => $stats[JobPostingInteraction::TYPE_VIEW] ?? 0,
            'apply_clicks' => $stats[JobPostingInteraction::TYPE_APPLY_CLICK] ?? 0,
            'email_reveals' => $stats[JobPostingInteraction::TYPE_EMAIL_REVEAL] ?? 0,
            'phone_reveals' => $stats[JobPostingInteraction::TYPE_PHONE_REVEAL] ?? 0,
            'downloads' => $stats[JobPostingInteraction::TYPE_DOWNLOAD] ?? 0,
            'total_interactions' => array_sum($stats),
        ];
    }

    /**
     * Get unique visitors count (based on unique IP addresses)
     */
    public function getUniqueVisitorsCount(): int
    {
        return $this->interactions()
            ->where('interaction_type', JobPostingInteraction::TYPE_VIEW)
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Check if this job posting is exempt from credit charges
     */
    public function isExemptFromCredits(): bool
    {
        $organization = $this->facility->organization;
        return JobPostingCreditExemption::hasExemption($this->employment_type, $organization);
    }

    /**
     * Get all SEO keywords including custom and auto-generated ones
     */
    public function getSeoKeywordsArray(): array
    {
        $keywords = [];

        // Add custom SEO keywords if provided
        if ($this->seo_keywords) {
            $customKeywords = array_map('trim', explode(',', $this->seo_keywords));
            $keywords = array_merge($keywords, $customKeywords);
        }

        // Add location from facility
        if ($this->facility && $this->facility->address) {
            $keywords[] = $this->facility->address->city;
        }

        // Add common education/school-related terms
        $commonTerms = [
            'Stelle',
            'Job',
            'Stellenangebot',
            'Stellenanzeige',
            'Bewerbung',
            'Karriere',
            'Schule',
            'Bildung',
            'Pädagogik',
            'Lehrkraft',
            'Lehrer',
            'Schuljob',
            'Schulstelle',
        ];
        $keywords = array_merge($keywords, $commonTerms);

        // Add employment type
        $keywords[] = $this->getEmploymentTypeLabel();

        // Add job category if available
        if ($this->job_category) {
            $keywords[] = $this->job_category;
        }

        // Remove duplicates and empty values
        $keywords = array_unique(array_filter($keywords));

        return $keywords;
    }

    /**
     * Get SEO meta description
     */
    public function getSeoMetaDescription(): string
    {
        $location = $this->facility && $this->facility->address ? $this->facility->address->city : '';
        $employmentType = $this->getEmploymentTypeLabel();

        $description = "{$this->title} ({$employmentType})";

        if ($location) {
            $description .= " in {$location}";
        }

        $description .= " bei {$this->facility->name}. ";

        // Add snippet from job description
        $descSnippet = strip_tags($this->description);
        $descSnippet = mb_substr($descSnippet, 0, 120);
        $description .= $descSnippet;

        // Limit to 155 characters for SEO best practices
        return mb_substr($description, 0, 155);
    }
}


