<?php

namespace App\Repositories;

use App\Models\Novel;

class NovelRepository{
    protected $model;

    public function __construct(Novel $model)
    {
        $this->model = $model;
    }

    public function getTopNovels($limit = 7)
    {
        return Novel::with('tags', 'user')
        ->orderBy('followers', 'desc')
        ->take(5)
        ->get()
        ->map(function ($novel) {
            return [
                'id' => $novel->id,
                'title' => $novel->title,
                'description' => $novel->description,
                'image_url' => $novel->image_url,
                'tags' => $novel->tags->map(function ($tag) {
                    return ['id' => $tag->id, 'name' => $tag->tag_name];
                }),
                'author_name' => $novel->user->name,
            ];
        });
    }
    public function getRandomNovels($limit = 15)
    {
        return Novel::inRandomOrder()->take(15)->get()
        ->map(function ($novel){
            return [
                'id' => $novel->id,
                'title' => $novel->title,
                'image_url' => $novel->image_url,
            ];
        });
    }
    public function getNovelById($id)
    {
        $rawNovel = Novel::with(['tags' , 'user', 'chapters' => function($query){
            $query->orderBy('chapter_number', 'asc');
        }])->findOrFail($id);
        $novel = [
            'id' => $rawNovel->id,
            'author_id' => $rawNovel->author_id,
            'status' => $rawNovel->status,
            'followers' => $rawNovel->followers,
            'number_of_chapters' => $rawNovel->number_of_chapters,
            'title' => $rawNovel->title,
            'description' => $rawNovel->description,
            'image_url' => $rawNovel->image_url,
            'tags' => $rawNovel->tags->map(function ($tag) {
                return ['id' => $tag->id, 'name' => $tag->tag_name];
            }),
            'author_name' => $rawNovel->user->name,
            'avatar_url' => $rawNovel->user->avatar_url,
            'chapters' => $rawNovel->chapters->map(function ($chapter) {
                   return [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'novel_id' => $chapter->novel_id,
                    'chapter_dnumber' => $chapter->chapter_number,
                    'updated_at' => $chapter->updated_at->format('d M Y'),
                ];
            }),
        ];
        return $novel;
    }
    public function createNovel($data)
    {
        return Novel::create($data);
    }
    public function updateNovel($novel, $data)
    {
        return $novel->update($data);
    }
}