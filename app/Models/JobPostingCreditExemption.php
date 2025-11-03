<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobPostingCreditExemption extends Model
{
    use HasFactory;

    const APPLIES_TO_ALL = 'all';
    const APPLIES_TO_COOPERATIVE_MEMBERS_ONLY = 'cooperative_members_only';

    protected $fillable = [
        'employment_type',
        'applies_to',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this exemption applies to a given organization
     */
    public function appliesTo(Organization $organization): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->applies_to === self::APPLIES_TO_ALL) {
            return true;
        }

        if ($this->applies_to === self::APPLIES_TO_COOPERATIVE_MEMBERS_ONLY) {
            return $organization->is_cooperative_member;
        }

        return false;
    }

    /**
     * Get a human-readable label for the applies_to field
     */
    public function getAppliesToLabel(): string
    {
        return match($this->applies_to) {
            self::APPLIES_TO_ALL => 'Alle Organisationen',
            self::APPLIES_TO_COOPERATIVE_MEMBERS_ONLY => 'Nur Genossenschaftsmitglieder',
            default => 'Unbekannt',
        };
    }

    /**
     * Get employment type label
     */
    public function getEmploymentTypeLabel(): string
    {
        return match($this->employment_type) {
            'full_time' => 'Vollzeit',
            'part_time' => 'Teilzeit',
            'mini_job' => 'Minijob',
            'internship' => 'Praktikum',
            'apprenticeship' => 'Ausbildung',
            'volunteer' => 'Ehrenamt',
            default => 'Unbekannt',
        };
    }

    /**
     * Check if an exemption exists for a given employment type and organization
     */
    public static function hasExemption(string $employmentType, Organization $organization): bool
    {
        $exemptions = self::where('employment_type', $employmentType)
            ->where('is_active', true)
            ->get();

        foreach ($exemptions as $exemption) {
            if ($exemption->appliesTo($organization)) {
                return true;
            }
        }

        return false;
    }
}
