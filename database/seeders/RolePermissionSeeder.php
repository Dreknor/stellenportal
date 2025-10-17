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

            // Admin: User Management
            'admin view users',
            'admin create users',
            'admin edit users',
            'admin delete users',
            'admin impersonate users',

            // Admin: Organization Management
            'admin view organizations',
            'admin create organizations',
            'admin edit organizations',
            'admin delete organizations',

            // Admin: Facility Management
            'admin view facilities',
            'admin create facilities',
            'admin edit facilities',
            'admin delete facilities',

            // Admin: Job Posting Management
            'admin view job postings',
            'admin edit job postings',
            'admin delete job postings',
            'admin publish job postings',

            // Admin: Credit Management
            'admin view credits',
            'admin manage credits',
            'admin grant credits',

            // Admin: Audit Logs
            'admin view logs',
            'admin export logs',

            // Admin: System Settings
            'admin view settings',
            'admin edit settings',
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

        // Admin Rolle erstellen
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        // Admin bekommt alle Admin-Permissions
        $adminPermissions = Permission::where('name', 'like', 'admin %')->get();
        $adminRole->givePermissionTo($adminPermissions);
    }
}
