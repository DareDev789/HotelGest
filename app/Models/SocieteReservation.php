<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteReservation extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_reservation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_reservation',
        'id_client',
        'id_agence',
        'date_debut',
        'date_fin',
        'id_hotel',
        'type_client',
        'reserv_par',
        'annule_par',
        'remise',
        'taux',
        'tva',
        'devise',
        'notes',
        'taxe',
        'etat_reservation',
        'statut_reservation',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function client()
    {
        return $this->belongsTo(SocieteClient::class, 'id_client', 'id');
    }

    public function agence()
    {
        return $this->belongsTo(SocieteAgence::class, 'id_agence', 'id');
    }

    public function detailsReservations()
    {
        return $this->hasMany(SocieteDetailsReservation::class, 'id_reservation', 'id_reservation');
    }
    public function detailsPrestations()
    {
        return $this->hasMany(SocieteDetailsPrestations::class, 'id_reservation', 'id_reservation');
    }
    public function detailsServicesDivers()
    {
        return $this->hasMany(SocieteDetailsReservationsDivers::class, 'id_reservation', 'id_reservation');
    }
    public function accomptes()
    {
        return $this->hasMany(SocieteAccomptesReservations::class, 'id_reservation', 'id_reservation');
    }
}
