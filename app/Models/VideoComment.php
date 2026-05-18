<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoComment extends Model
{
    protected $fillable = [
        'video_id',
        'nama',
        'komentar',
        'is_approved',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
