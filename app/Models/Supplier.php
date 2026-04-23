<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['nama_supplier', 'no_telp', 'alamat'];

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }
}
