<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'novel_id' => \App\Models\Novel::factory(), // for test
            'author_id' => \App\Models\User::factory(), // for test
            // 'novel_id' => 2, // Example ID, replace with actual logic if needed
            // 'author_id' => 59, // Example ID, replace with actual logic if needed
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(10, true),
            'chapter_number' => $this->faker->numberBetween(1, 100),
        ];
    }
}
