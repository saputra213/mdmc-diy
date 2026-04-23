<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $fillable = ['nama_lokasi', 'alamat'];

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }
}
