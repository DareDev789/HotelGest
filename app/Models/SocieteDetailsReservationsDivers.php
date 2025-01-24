<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDetailsReservationsDivers extends Model
{
    use SoftDeletes;

    protected $table = 'societe_reservations_divers';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'designation',
        'id_reservation',
        'id_hotel',
        'prix_jour',
        'pack',
        'id_diver',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function diver()
    {
        return $this->belongsTo(SocieteServicesDivers::class, 'id_diver', 'id');
    }

    public function Reservations()
    {
        return $this->belongsTo(SocieteReservation::class, 'id_reservation', 'id_reservation');
    }
}
