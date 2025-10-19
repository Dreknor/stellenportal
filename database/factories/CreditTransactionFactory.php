<?php

namespace Database\Factories;

use App\Models\CreditTransaction;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditTransactionFactory extends Factory
{
    protected $model = CreditTransaction::class;

    public function definition(): array
    {
        return [
            'creditable_id' => Organization::factory(),
            'creditable_type' => Organization::class,
            'user_id' => User::factory(),
            'type' => fake()->randomElement([
                CreditTransaction::TYPE_PURCHASE,
                CreditTransaction::TYPE_USAGE,
                CreditTransaction::TYPE_ADJUSTMENT,
            ]),
            'amount' => fake()->numberBetween(-10, 50),
            'balance_after' => fake()->numberBetween(0, 100),
            'note' => fake()->sentence(),
        ];
    }

    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CreditTransaction::TYPE_PURCHASE,
            'amount' => fake()->numberBetween(5, 50),
            'price_paid' => fake()->randomFloat(2, 50, 500),
        ]);
    }

    public function usage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CreditTransaction::TYPE_USAGE,
            'amount' => -1,
        ]);
    }
}

