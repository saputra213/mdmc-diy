<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'disaster_event_id',
        'supplier_id',
        'barang_id',
        'user_id',
        'jumlah_masuk',
        'tanggal_masuk',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
