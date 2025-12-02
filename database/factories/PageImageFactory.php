<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageImage>
 */
class PageImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->uuid() . '.jpg';

        return [
            'page_id' => \App\Models\Page::factory(),
            'filename' => $filename,
            'original_filename' => fake()->word() . '.jpg',
            'path' => 'pages/1/' . $filename,
            'size' => fake()->numberBetween(50000, 5000000),
            'mime_type' => 'image/jpeg',
            'alt_text' => fake()->sentence(3),
            'title' => fake()->sentence(2),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
