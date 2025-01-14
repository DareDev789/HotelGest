<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotel';

    protected $primaryKey = 'ID';

    public $timestamps = true;

    protected $fillable = [
        'ID',
        'nom_etablissement',
        'gerant_etablissement',
        'id_hotel',
        'adresse',
        'telephone',
        'e_mail',
        'site_web',
        'categorie',
        'entete1',
        'pied1',
        'entete2',
        'photo_profil',
        'pied2',
        'valid',
        'date_expiration',
        'date_inscription',
        'ville',
        'pays',
        'nom_societe',
        'siege',
        'prefixe',
        'suffixe',
        'taxe',
    ];
}
