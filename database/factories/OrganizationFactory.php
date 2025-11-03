<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'description' => fake()->paragraph(),
            'is_cooperative_member' => false,
        ];
    }

    /**
     * Indicate that the organization is a cooperative member.
     */
    public function cooperativeMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cooperative_member' => true,
        ]);
    }
}

