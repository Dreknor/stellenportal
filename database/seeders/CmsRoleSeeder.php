<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CmsRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create CMS role if it doesn't exist
        $cmsRole = Role::firstOrCreate([
            'name' => 'cms',
            'guard_name' => 'web',
        ]);

        // Define CMS permissions
        $cmsPermissions = [
            'admin view pages',
            'admin create pages',
            'admin edit pages',
            'admin delete pages',
            'admin publish pages',
            'admin manage page images',
            'admin manage menus',
        ];

        // Ensure all permissions exist
        foreach ($cmsPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Assign all CMS permissions to CMS role
        $cmsRole->syncPermissions($cmsPermissions);

        $this->command->info('CMS role created with all CMS permissions.');
    }
}

