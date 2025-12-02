<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasPage = fake()->boolean(70);

        return [
            'menu_location' => fake()->randomElement(['header', 'footer']),
            'parent_id' => null,
            'page_id' => $hasPage ? \App\Models\Page::factory() : null,
            'label' => fake()->words(2, true),
            'url' => $hasPage ? null : fake()->url(),
            'target' => fake()->randomElement(['_self', '_blank']),
            'order' => fake()->numberBetween(0, 10),
            'is_active' => fake()->boolean(90),
            'css_class' => fake()->optional()->word(),
            'icon' => fake()->optional()->word(),
        ];
    }

    /**
     * Indicate that the menu item is a child.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => \App\Models\MenuItem::factory(),
        ]);
    }

    /**
     * Indicate that the menu item links to a page.
     */
    public function withPage(): static
    {
        return $this->state(fn (array $attributes) => [
            'page_id' => \App\Models\Page::factory(),
            'url' => null,
        ]);
    }

    /**
     * Indicate that the menu item has an external URL.
     */
    public function withUrl(): static
    {
        return $this->state(fn (array $attributes) => [
            'page_id' => null,
            'url' => fake()->url(),
        ]);
    }
}
