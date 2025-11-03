<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class CreditPackageSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Standard-Pakete für Nicht-Genossenschaftsmitglieder
        $standardPackages = [
            [
                'name' => 'Starter-Paket',
                'description' => 'Ideal für kleine Einrichtungen',
                'credits' => 10,
                'price' => 49.99,
                'is_active' => true,
                'for_cooperative_members' => false,
            ],
            [
                'name' => 'Basic-Paket',
                'description' => 'Optimal für mittlere Einrichtungen',
                'credits' => 25,
                'price' => 99.99,
                'is_active' => true,
                'for_cooperative_members' => false,
            ],
            [
                'name' => 'Professional-Paket',
                'description' => 'Perfekt für größere Einrichtungen',
                'credits' => 50,
                'price' => 179.99,
                'is_active' => true,
                'for_cooperative_members' => false,
            ],
            [
                'name' => 'Premium-Paket',
                'description' => 'Für Organisationen mit mehreren Einrichtungen',
                'credits' => 100,
                'price' => 299.99,
                'is_active' => true,
                'for_cooperative_members' => false,
            ],
            [
                'name' => 'Enterprise-Paket',
                'description' => 'Das ultimative Paket für große Organisationen',
                'credits' => 250,
                'price' => 649.99,
                'is_active' => true,
                'for_cooperative_members' => false,
            ],
        ];

        // Spezial-Pakete für Genossenschaftsmitglieder (mit vergünstigten Preisen)
        $cooperativePackages = [
            [
                'name' => 'Genossenschaft Starter',
                'description' => 'Vergünstigtes Starter-Paket für Genossenschaftsmitglieder',
                'credits' => 5,
                'price' => 20.00,
                'is_active' => true,
                'for_cooperative_members' => true,
            ],
            [
                'name' => 'Genossenschaft Starter',
                'description' => 'Vergünstigtes Starter-Paket für Genossenschaftsmitglieder',
                'credits' => 10,
                'price' => 39.99,
                'is_active' => true,
                'for_cooperative_members' => true,
            ],
            [
                'name' => 'Genossenschaft Basic',
                'description' => 'Vergünstigtes Basic-Paket für Genossenschaftsmitglieder',
                'credits' => 25,
                'price' => 79.99,
                'is_active' => true,
                'for_cooperative_members' => true,
            ]
        ];

        foreach ($standardPackages as $package) {
            CreditPackage::create($package);
        }

        foreach ($cooperativePackages as $package) {
            CreditPackage::create($package);
        }
    }
}

