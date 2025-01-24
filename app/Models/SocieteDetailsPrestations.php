<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDetailsPrestations extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_reservation',
        'nb_personne',
        'id_prestation',
        'prix_prestation',
        'date_in',
        'date_out',
        'prestation',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function prestation()
    {
        return $this->belongsTo(SocietePrestations::class, 'id_prestation', 'id');
    }

    public function Reservations()
    {
        return $this->belongsTo(SocieteReservation::class, 'id_reservation', 'id_reservation');
    }
}
