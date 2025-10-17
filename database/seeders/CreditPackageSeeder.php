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
        $packages = [
            [
                'name' => 'Starter-Paket',
                'description' => 'Ideal für kleine Einrichtungen',
                'credits' => 100,
                'price' => 49.99,
                'is_active' => true,
            ],
            [
                'name' => 'Basic-Paket',
                'description' => 'Optimal für mittlere Einrichtungen',
                'credits' => 250,
                'price' => 99.99,
                'is_active' => true,
            ],
            [
                'name' => 'Professional-Paket',
                'description' => 'Perfekt für größere Einrichtungen',
                'credits' => 500,
                'price' => 179.99,
                'is_active' => true,
            ],
            [
                'name' => 'Premium-Paket',
                'description' => 'Für Organisationen mit mehreren Einrichtungen',
                'credits' => 1000,
                'price' => 299.99,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise-Paket',
                'description' => 'Das ultimative Paket für große Organisationen',
                'credits' => 2500,
                'price' => 649.99,
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            CreditPackage::create($package);
        }
    }
}

