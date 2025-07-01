<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'tag_name',
    ];
    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'novel_tags');
    }
}
