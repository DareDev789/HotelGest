<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationsDivers extends Model
{

    protected $table = 'details_commande_Divers';

    protected $fillable = [
        'id_commande', 
        'designation',
        'prix_jour',
        'pack',
        'id_hotel',
        'ident_reservation',
        'date_commande',
    ];
}
