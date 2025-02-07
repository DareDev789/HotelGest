<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDetailsCommandesProduits extends Model
{
    use SoftDeletes;

    protected $table = 'societe_details_commande_produit';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';

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
