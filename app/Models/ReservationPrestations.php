<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationPrestations extends Model
{

    protected $table = 'reservation_prestation';
    protected $primaryKey = 'ID_rPrestation ';
    public $incrementing = true;

    protected $fillable = [
        'ident_reservation',
        'nb_personne',
        'id_rprestation',
        'prix_prestation',
        'date_in',
        'date_out',
        'remise',
        'prestation',
        'id_hotel'
    ];
}
