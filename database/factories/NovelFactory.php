<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Novel;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Novel>
 */
class NovelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Novel::class;
    public function definition(): array
    {
        $this->faker = \Faker\Factory::create('en_US');
        $img_urls = [
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_0.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_1.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_2.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_3.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_4.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_5.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_6.webp',
            'https://res.cloudinary.com/dvomghpsu/image/upload/v1739376003/novel_project/cover_image/cover_image_sample_7.webp',
        ];
        $index = rand(0, 7);
        return [
            'author_id' => rand(1, 100),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'image_url' => $img_urls[$index],
            'image_public_id' => 'novel_project/cover_image/cover_image_sample_' . $index,
            'status' => 'ongoing',
            'followers' => $this->faker->numberBetween(0, 90),
            'number_of_chapters' => 0,
        ];
    }
}
