<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    /**
     * Determine if the user can view any job postings
     */
    public function viewAny(User $user): bool
    {
        // Users can view job postings of their facilities
        return $user->facilities()->exists() || $user->organizations()->exists();
    }

    /**
     * Determine if the user can view the job posting
     */
    public function view(User $user, JobPosting $jobPosting): bool
    {
        // Public view is always allowed for active postings
        if ($jobPosting->isActive()) {
            return true;
        }

        // User must be part of the facility or organization
        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Determine if the user can create job postings
     */
    public function create(User $user): bool
    {
        // Check if user has at least one approved organization
        $hasApprovedOrg = $user->organizations()->where('is_approved', true)->exists();

        // Or check if user has facilities with approved organizations
        $hasApprovedFacility = $user->facilities()
            ->whereHas('organization', function ($query) {
                $query->where('is_approved', true);
            })
            ->exists();

        return $hasApprovedOrg || $hasApprovedFacility;
    }

    /**
     * Determine if the user can update the job posting
     */
    public function update(User $user, JobPosting $jobPosting): bool
    {
        // Organization must be approved
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return false;
        }

        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Determine if the user can delete the job posting
     */
    public function delete(User $user, JobPosting $jobPosting): bool
    {
        // Organization must be approved
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return false;
        }

        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Determine if the user can publish the job posting
     */
    public function publish(User $user, JobPosting $jobPosting): bool
    {
        // Organization must be approved
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return false;
        }

        // Only check if user belongs to facility or organization
        // Credit check is handled in the service layer for better error messaging
        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Determine if the user can extend the job posting
     */
    public function extend(User $user, JobPosting $jobPosting): bool
    {
        // Organization must be approved
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return false;
        }

        // Only check if user belongs to facility or organization
        // Credit check is handled in the service layer for better error messaging
        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Determine if the user can pause the job posting
     */
    public function pause(User $user, JobPosting $jobPosting): bool
    {
        // Organization must be approved
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return false;
        }

        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Determine if the user can resume the job posting
     */
    public function resume(User $user, JobPosting $jobPosting): bool
    {
        // Organization must be approved
        if (!$jobPosting->facility->organization->canUseFeatures()) {
            return false;
        }

        return $this->belongsToFacilityOrOrganization($user, $jobPosting);
    }

    /**
     * Check if user belongs to the facility or its organization
     */
    protected function belongsToFacilityOrOrganization(User $user, JobPosting $jobPosting): bool
    {
        $facility = $jobPosting->facility;

        // Check if user belongs to the facility
        if ($user->facilities()->where('facilities.id', $facility->id)->exists()) {
            return true;
        }

        // Check if user belongs to the organization that owns the facility
        if ($facility->organization_id && $user->organizations()->where('organizations.id', $facility->organization_id)->exists()) {
            return true;
        }

        // Admins can manage all postings
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }
}
