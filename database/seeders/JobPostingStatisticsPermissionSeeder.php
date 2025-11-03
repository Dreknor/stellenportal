<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class JobPostingStatisticsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permission
        $permission = Permission::firstOrCreate(
            ['name' => 'view job posting statistics'],
            ['guard_name' => 'web']
        );

        // Assign to admin roles
        $adminRoles = [
            'Super Admin',
            'Admin',
        ];

        foreach ($adminRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && !$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
                $this->command->info("Permission 'view job posting statistics' assigned to role '{$roleName}'");
            }
        }


        $this->command->info('Job Posting Statistics permission seeded successfully!');
    }
}


