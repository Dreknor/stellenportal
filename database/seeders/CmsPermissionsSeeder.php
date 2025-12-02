<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CmsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define CMS permissions
        $permissions = [
            'admin view pages',
            'admin create pages',
            'admin edit pages',
            'admin delete pages',
            'admin publish pages',
            'admin manage page images',
            'admin manage menus',
        ];

        // Create permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Assign all CMS permissions to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('CMS permissions created and assigned to admin role.');
    }
}

