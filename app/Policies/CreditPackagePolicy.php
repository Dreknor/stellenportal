<?php

namespace App\Policies;

use App\Models\CreditPackage;
use App\Models\User;

class CreditPackagePolicy
{
    /**
     * Determine whether the user can view any credit packages.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage credit packages');
    }

    /**
     * Determine whether the user can create credit packages.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage credit packages');
    }

    /**
     * Determine whether the user can update the credit package.
     */
    public function update(User $user, CreditPackage $creditPackage): bool
    {
        return $user->hasPermissionTo('manage credit packages');
    }

    /**
     * Determine whether the user can delete the credit package.
     */
    public function delete(User $user, CreditPackage $creditPackage): bool
    {
        return $user->hasPermissionTo('manage credit packages');
    }
}

