<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservations extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservations';
    protected $primaryKey = 'id_reservation';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'id_client',
        'nom_client',
        'infos_client',
        'date_debut',
        'date_fin',
        'id_hotel',
        'type_client',
        'id_agence',
        'nom_agence',
        'infos_agence',
        'id_bungalow',
        'prix_bungalow',
        'bungalow_designation',
        'autres_info_bungalow',
        'num_bungalow',
        'type_bungalow',
        'ident_reservation',
        'nb_personne',
        'date_reservation',
        'etat_reservation', 
        'type_reser',
        'annule_par',
        'reserv_par',
        'remise',
        'taux',
        'tva',
        'devise',
        'bonConfirmation',
        'notes',
        'litsup_prix',
        'litsup_nombre',
        'taxe',
    ];

    protected $casts = [
        'prix_bungalow' => 'float',
    ];

    /**
     * Relation avec le modèle Hotel.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }

    /**
     * Formater le prix du bungalow.
     */
    public function getPrixBungalowAttribute($value)
    {
        return number_format($value, 2, '.', ' ');
    }
    public function bungalow()
    {
        return $this->belongsTo(Bungalows::class, 'id_bungalow', 'ID');
    }

}

