<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $table = 'jenis';
    protected $fillable = ['nama_jenis'];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
