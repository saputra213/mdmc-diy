<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BisindoMaterial extends Model
{
    protected $fillable = [
        'judul',
        'kategori',
        'tingkat',
        'gambar_url',
        'video_url',
        'deskripsi',
        'sumber_jurnal',
        'is_active',
    ];
}
