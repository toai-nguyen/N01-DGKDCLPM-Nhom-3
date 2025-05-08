<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = [
        'novel_id',
        'user_id',
        'author_id',
        'title',
        'content',
        'chapter_number',
    ];
    public function novel()
    {
        return $this->belongsTo(Novel::class, 'novel_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
