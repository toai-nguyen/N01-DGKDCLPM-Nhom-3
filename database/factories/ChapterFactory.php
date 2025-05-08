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
            'novel_id' => '2',
            'author_id' => '59',
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(5, true),
            'chapter_number' => $this->faker->unique->numberBetween(1, 10),
        ];
    }
}
