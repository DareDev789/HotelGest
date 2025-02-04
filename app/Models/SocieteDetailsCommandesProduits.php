<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDetailsCommandesProduits extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id_commande',
        'quantite',
        'id_produit',
        'prix_produit',
        'nom_produit',
        'id_hotel',
        'save_by',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function produit()
    {
        return $this->belongsTo(SocieteProduit::class, 'id_produit', 'id');
    }

    public function commande()
    {
        return $this->belongsTo(SocieteCommande::class, 'id_commande', 'id_commande');
    }
    public function user()
    {
        return $this->belongsTo(SocieteUser::class, 'save_by', 'id');
    }
}
