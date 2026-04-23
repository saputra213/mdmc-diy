<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BantuanRequest extends Model
{
    protected $fillable = [
        'nama_pelapor',
        'no_hp',
        'lokasi_detail',
        'jenis_bantuan',
        'status',
    ];

    protected $casts = [
        'jenis_bantuan' => 'array',
    ];
}
