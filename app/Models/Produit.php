<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    protected $table = 'produits';
    protected $primaryKey = 'ID';
    public $incrementing = true;

    protected $fillable = [
        'nom_produit',
        'prix_achat',
        'prix_vente',
        'categorie_id',
        'date_ajout',
        'quantite_stock',
        'ajout_by',
        'etat',
        'quantifie',
    ];
}
