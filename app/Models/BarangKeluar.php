<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'disaster_event_id',
        'user_id',
        'barang_id',
        'jumlah_keluar',
        'tanggal_keluar',
        'lokasi_id',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
