<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom_client',
        'adresse',
        'email',
        'telephone',
        'id_hotel',
        'autres_info_client',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function reservations()
    {
        return $this->hasMany(SocieteReservation::class, 'id_client', 'id');
    }
}
