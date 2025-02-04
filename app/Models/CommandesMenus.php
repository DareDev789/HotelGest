<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommandesMenus extends Model
{

    protected $table = 'details_commande_menu';
    protected $primaryKey = 'ID';
    public $incrementing = true;

    protected $fillable = [
        'nom_menu',
        'quantite',
        'prix_menu',
        'prix_total',
        'id_hotel',
        'ident_reservation',
        'date_commande',
        'commande_par',
    ];
}
