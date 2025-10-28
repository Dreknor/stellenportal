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
            // User must be assigned to the facility
            return $user->facilities->contains($creditable);
        }

        if ($creditable instanceof Organization) {
            // Organization must be approved
            if (!$creditable->canUseFeatures()) {
                return false;
            }
            // User must be assigned to the organization
            return $user->organizations->contains($creditable);
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
        return $user->organizations->contains($organization);
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
            return $user->facilities->contains($creditable)
                || $user->organizations->contains($creditable->organization);
        }

        if ($creditable instanceof Organization) {
            // Organization must be approved
            if (!$creditable->canUseFeatures()) {
                return false;
            }
            // User must be assigned to the organization
            return $user->organizations->contains($creditable);
        }

        return false;
    }
}
