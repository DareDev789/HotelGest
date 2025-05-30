<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteAccomptesReservations extends Model
{
    use SoftDeletes;

    protected $table = 'societe_accomptes_reservations';

    protected $fillable = [
        'id',
        'id_reservation',
        'id_hotel',
        'montant',
        'save_by',
        'paid',
        'facture_id',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
    public function reservation()
    {
        return $this->belongsTo(SocieteReservation::class, 'id_reservation', 'id_reservation');
    }
    public function utilisateur()
    {
        return $this->belongsTo(SocieteUser::class, 'save_by', 'id');
    }
    public function facture()
    {
        return $this->hasOne(SocieteFactures::class, 'id_accompte', 'id');
    }

}
