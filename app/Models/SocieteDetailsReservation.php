<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDetailsReservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_reservation',
        'id_bungalow',
        'type_bungalow',
        'prix_bungalow',
        'id_hotel',
        'nb_personne',
    ];

    public function reservation()
    {
        return $this->belongsTo(SocieteReservation::class, 'id_reservation', 'id_reservation');
    }

    public function bungalow()
    {
        return $this->belongsTo(SocieteBungalow::class, 'id_bungalow', 'id');
    }

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
