<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //list of tags
        $tags = [
            'Action',
            'Adventure',
            'Comedy',
            'Crime',
            'Drama',
            'Fantasy',
            'Historical',
            'Horror',
            'Isekai',
            'Mecha',
            'Mystery',
            'Medical',
            'Psychological',
            'Philosophical',
            'Romance',
            'Sci-fi',
            'Slice of Life',
            'Sports',
            'Superhero',
            'Thriller',
            'Tragedy',
            'Wuxia',
        ];

        //insert tags into the database
        foreach ($tags as $tag) {
            Tag::create(['tag_name' => $tag]);
        }
    }
}
