<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kebutuhan extends Model
{
    protected $fillable = [
        'nama_lokasi',
        'jumlah_korban',
        'nama_barang',
        'butuh_barang',
        'status',
    ];
}
