<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteProduitStock extends Model
{
    use SoftDeletes;

    protected $table = 'societe_produit_stock';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_produit',
        'stock',
        'prix_achat',
        'id_user',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }
    public function Produit()
    {
        return $this->belongsTo(SocieteCategorieProduit::class, 'id_produit', 'id');
    }
    public function User() 
    {
        return $this->belongsTo(SocieteUser::class, 'id_user', 'id');
    }
}
