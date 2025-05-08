<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Novel;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class NovelTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //get all novel ids and tag ids
        $novelIds = Novel::pluck('id')->toArray();
        $tagIds = Tag::pluck('id')->toArray();

        foreach ($novelIds as $novelId) {
            // Gán ngẫu nhiên từ 1 đến 10 tags cho mỗi novel
            $randomTagIds = array_rand(array_flip($tagIds), rand(1, 10));
            if (!is_array($randomTagIds)) {
                $randomTagIds = [$randomTagIds];
            }

            foreach ($randomTagIds as $tagId) {
                DB::table('novel_tags')->insert([
                    'novel_id' => $novelId,
                    'tag_id' => $tagId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
