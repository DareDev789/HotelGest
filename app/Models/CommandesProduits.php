<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommandesProduits extends Model
{

    protected $table = 'details_commande';
    protected $primaryKey = 'id_commande';
    public $incrementing = true;

    protected $fillable = [
        'nom_produit',
        'quantite',
        'prix_unitaire',
        'prix_total',
        'date_commande',
        'id_hotel',
        'ident_reservation',
        'commande_par',
    ];
}
