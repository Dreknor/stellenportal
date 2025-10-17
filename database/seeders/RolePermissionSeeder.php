<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions für Rollen-Verwaltung
        $permissions = [
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',

            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Super Admin Rolle erstellen
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        // Super Admin bekommt alle Permissions
        $superAdminRole->givePermissionTo(Permission::all());
    }
}

