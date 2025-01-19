<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteReservation extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_reservation';
    public $incrementing = false; // UUID utilisé comme clé primaire
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
}
