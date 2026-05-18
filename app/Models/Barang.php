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
        'disaster_event_id',
        'nama_barang',
        'stok',
        'satuan_id',
        'jenis_id',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }
}
