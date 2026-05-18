<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisasterEvent extends Model
{
    protected $fillable = [
        'name',
        'location',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function kebutuhans()
    {
        return $this->hasMany(Kebutuhan::class);
    }

    public function bantuanRequests()
    {
        return $this->hasMany(BantuanRequest::class);
    }

    public function photos()
    {
        return $this->hasMany(DisasterEventPhoto::class);
    }
}
