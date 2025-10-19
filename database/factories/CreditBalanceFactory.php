<?php

namespace Database\Factories;

use App\Models\CreditBalance;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditBalanceFactory extends Factory
{
    protected $model = CreditBalance::class;

    public function definition(): array
    {
        return [
            'creditable_id' => Organization::factory(),
            'creditable_type' => Organization::class,
            'balance' => fake()->numberBetween(0, 100),
        ];
    }
}

