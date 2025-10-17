<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view roles');
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('view roles');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create roles');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('edit roles');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Super Admin Rolle darf nicht gelöscht werden
        if ($role->name === 'Super Admin') {
            return false;
        }

        return $user->hasPermissionTo('delete roles');
    }

    /**
     * Determine whether the user can assign roles to users.
     */
    public function assign(User $user): bool
    {
        return $user->hasPermissionTo('assign roles');
    }
}

