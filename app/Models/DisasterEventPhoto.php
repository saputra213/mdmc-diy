<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisasterEventPhoto extends Model
{
    protected $fillable = [
        'disaster_event_id',
        'image_path',
        'description',
    ];

    public function disasterEvent()
    {
        return $this->belongsTo(DisasterEvent::class);
    }
}

