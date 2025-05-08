<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NovelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author_id' => $this->author_id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'image_public_id' => $this->image_public_id,
            'status' => $this->status,
            'followers' => $this->followers,
            'number_of_chapters' => $this->number_of_chapters,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
