<?php

namespace App\Repositories;

use App\Models\Chapter;

class ChapterRepository{
    protected $model;

    public function __construct(Chapter $model)
    {
        $this->model = $model;
    }

    public function getLastestChapters($limit = 12)
    {
        return Chapter::with('novel', 'novel.user')
        ->orderBy('updated_at', 'desc')
        ->take(12)
        ->get()
        ->map(function ($chapter) {
            return [
                'chapter_id' => $chapter->id,
                'chapter_number' => $chapter->chapter_number,
                'novel_id' => $chapter->novel->id,
                'author_id' => $chapter->novel->author_id,
                'title' => $chapter->novel->title,
                'image_url' => $chapter->novel->image_url,
                'author_name' => $chapter->novel->user->name,
            ];
        });
    }
}