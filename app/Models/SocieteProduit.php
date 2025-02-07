<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteProduit extends Model
{
    use SoftDeletes;

    protected $table = 'societe_produit';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_categorie',
        'nom_produit',
        'prix_vente', 
        'quantifie',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }
    public function categorie()
    {
        return $this->belongsTo(SocieteCategorieProduit::class, 'id_categorie', 'id');
    }
    public function Stock()
    {
        return $this->hasMany(SocieteProduitStock::class, 'id', 'id_produit');
    }
    public function commandes()
    {
        return $this->hasMany(SocieteDetailsCommandesProduits::class, 'id', 'id_produit');
    }
}
