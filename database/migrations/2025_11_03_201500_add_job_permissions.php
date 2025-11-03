<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobPermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use the Spatie models to create permissions and assign them to Super Admin
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if they don't exist
        $perm1 = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'admin manage jobs',
            'guard_name' => 'web',
        ]);

        $perm2 = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'admin view logs',
            'guard_name' => 'web',
        ]);

        // Assign to Super Admin role (create role if it doesn't exist)
        $super = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $super->givePermissionTo([$perm1, $perm2]);

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove permissions and revoke from roles
        $names = ['admin manage jobs', 'admin view logs'];

        foreach ($names as $name) {
            $permission = \Spatie\Permission\Models\Permission::where('name', $name)->first();
            if ($permission) {
                // detach from roles
                foreach ($permission->roles as $role) {
                    $role->revokePermissionTo($permission);
                }
                $permission->delete();
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

