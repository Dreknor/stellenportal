<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AssignAllPermissionsToUserOneSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Benutzer mit ID 1 finden
        $user = User::find(1);

        if (!$user) {
            $this->command->error('Benutzer mit ID 1 wurde nicht gefunden!');
            return;
        }

        // Alle Berechtigungen abrufen
        $allPermissions = Permission::all();

        if ($allPermissions->isEmpty()) {
            $this->command->warn('Keine Berechtigungen in der Datenbank gefunden!');
            return;
        }

        // Alle Berechtigungen dem Benutzer zuweisen
        $user->givePermissionTo($allPermissions);

        $this->command->info("Dem Benutzer '{$user->first_name} {$user->last_name}' (ID: {$user->id}) wurden {$allPermissions->count()} Berechtigungen zugewiesen.");
    }
}

