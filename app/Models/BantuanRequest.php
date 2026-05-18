<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BantuanRequest extends Model
{
    protected $fillable = [
        'disaster_event_id',
        'nama_pelapor',
        'no_hp',
        'lokasi_detail',
        'jenis_bantuan',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'jenis_bantuan' => 'array',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }
}
