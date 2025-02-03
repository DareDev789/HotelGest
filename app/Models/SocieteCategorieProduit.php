<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteCategorieProduit extends Model
{
    use SoftDeletes;
    protected $table = 'societe_categorie_produit'; 
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'nom_categorie_produit',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }
    public function Produits()
    {
        return $this->hasMany(SocieteProduit::class, 'id_categorie', 'id');
    }
}
