<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteHotel extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_hotel';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_hotel',
        'nom_etablissement',
        'gerant_etablissement',
        'adresse',
        'email',
        'site_web',
        'date_inscription',
        'date_expiration',
        'ville',
        'pays',
        'nom_societe',
        'logo',
    ];

    public function users()
    {
        return $this->hasMany(SocieteUser::class, 'id_hotel', 'id_hotel');
    }

    public function clients()
    {
        return $this->hasMany(SocieteClient::class, 'id_hotel', 'id_hotel');
    }

    public function agences()
    {
        return $this->hasMany(SocieteAgence::class, 'id_hotel', 'id_hotel');
    }

    public function bungalows()
    {
        return $this->hasMany(SocieteBungalow::class, 'id_hotel', 'id_hotel');
    }

    public function reservations()
    {
        return $this->hasMany(SocieteReservation::class, 'id_hotel', 'id_hotel');
    }
}
