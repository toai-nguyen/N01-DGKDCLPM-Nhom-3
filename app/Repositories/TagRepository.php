<?php

namespace App\Repositories;

use App\Models\Tag;

class TagRepository{
    protected $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }
    public function getAll()
    {
        return Tag::all()
        ->map(function ($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->tag_name,
            ];
        });
    }
    public function storeTag($data)
    {
        return Tag::create($data);
    }
}