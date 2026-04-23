<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_barang',
        'stok',
        'satuan_id',
        'jenis_id',
    ];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }
}
