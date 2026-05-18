<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kebutuhan extends Model
{
    protected $fillable = [
        'disaster_event_id',
        'nama_lokasi',
        'jumlah_korban',
        'nama_barang',
        'butuh_barang',
        'status',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }
}
