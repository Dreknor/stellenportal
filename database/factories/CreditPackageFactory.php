<?php

namespace Database\Factories;

use App\Models\CreditPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditPackageFactory extends Factory
{
    protected $model = CreditPackage::class;

    public function definition(): array
    {
        $credits = fake()->randomElement([5, 10, 25, 50, 100]);

        return [
            'name' => "Paket {$credits} Credits",
            'description' => fake()->sentence(),
            'credits' => $credits,
            'price' => $credits * fake()->randomFloat(2, 8, 12),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

