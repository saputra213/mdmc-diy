<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'video_embed_url',
        'bisindo_embed_url',
        'views_count',
        'is_active',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(VideoComment::class);
    }
}
