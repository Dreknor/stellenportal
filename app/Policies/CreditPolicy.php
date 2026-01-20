<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\Organization;
use App\Models\User;

class CreditPolicy
{
    /**
     * Determine whether the user can purchase credits for a facility.
     */
    public function purchaseCredits(User $user, $creditable): bool
    {
        if ($creditable instanceof Facility) {
            // Organization must be approved
            if (!$creditable->organization->canUseFeatures()) {
                return false;
            }
            // User must be assigned to the facility OR to the facility's organization
            return $user->facilities()->where('facilities.id', $creditable->id)->exists()
                || $user->organizations()->where('organizations.id', $creditable->organization_id)->exists();
        }

        if ($creditable instanceof Organization) {
            // Organization must be approved
            if (!$creditable->canUseFeatures()) {
                return false;
            }
            // User must be assigned to the organization
            return $user->organizations()->where('organizations.id', $creditable->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can transfer credits from organization to facility.
     */
    public function transferCredits(User $user, Organization $organization): bool
    {
        // Organization must be approved
        if (!$organization->canUseFeatures()) {
            return false;
        }
        // User must be assigned to the organization
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    /**
     * Determine whether the user can transfer credits from facility to organization.
     */
    public function transferCreditsToOrganization(User $user, Facility $facility): bool
    {
        // Organization must be approved
        if (!$facility->organization->canUseFeatures()) {
            return false;
        }
        // User must be assigned to the facility OR to the facility's organization
        return $user->facilities()->where('facilities.id', $facility->id)->exists()
            || $user->organizations()->where('organizations.id', $facility->organization_id)->exists();
    }

    /**
     * Determine whether the user can view transactions.
     */
    public function viewTransactions(User $user, $creditable): bool
    {
        if ($creditable instanceof Facility) {
            // Organization must be approved
            if (!$creditable->organization->canUseFeatures()) {
                return false;
            }
            // User must be assigned to the facility or its organization
            return $user->facilities()->where('facilities.id', $creditable->id)->exists()
                || $user->organizations()->where('organizations.id', $creditable->organization_id)->exists();
        }

        if ($creditable instanceof Organization) {
            // Organization must be approved
            if (!$creditable->canUseFeatures()) {
                return false;
            }
            // User must be assigned to the organization
            return $user->organizations()->where('organizations.id', $creditable->id)->exists();
        }

        return false;
    }
}
