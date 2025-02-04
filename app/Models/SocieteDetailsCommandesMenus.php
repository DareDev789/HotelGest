<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDetailsCommandesMenus extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id_commande',
        'quantite',
        'id_menu',
        'prix_menu',
        'nom_menu',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function menu()
    {
        return $this->belongsTo(SocieteMenu::class, 'id_menu', 'id');
    }

    public function commande()
    {
        return $this->belongsTo(SocieteCommande::class, 'id_commande', 'id_commande');
    }
}
