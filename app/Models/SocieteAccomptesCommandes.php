<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteAccomptesCommandes extends Model
{
    use SoftDeletes;

    protected $table = 'societe_accomptes_commandes';

    protected $fillable = [
        'id',
        'id_commande',
        'id_hotel',
        'montant',
        'save_by',
        'paid',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
    public function Commande()
    {
        return $this->belongsTo(SocieteCommande::class, 'id_commande', 'id_commande');
    }
    public function user()
    {
        return $this->belongsTo(SocieteUser::class, 'save_by', 'id');
    }
}
