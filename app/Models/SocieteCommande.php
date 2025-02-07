<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteCommande extends Model
{
    use SoftDeletes;

    protected $table = 'societe_commande';

    protected $primaryKey = 'id_commande';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_client',
        'id_agence',
        'id_hotel',
        'type_client',
        'statut_reservation',
        'devise',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function client()
    {
        return $this->belongsTo(SocieteClient::class, 'id_client', 'id');
    }

    public function agence()
    {
        return $this->belongsTo(SocieteAgence::class, 'id_agence', 'id');
    }

    public function detailsCommandesProduits()
    {
        return $this->hasMany(SocieteDetailsCommandesProduits::class, 'id_commande', 'id_commande');
    }
    public function detailsCommandesMenus()
    {
        return $this->hasMany(SocieteDetailsCommandesMenus::class, 'id_commande', 'id_commande');
    }
    // public function accomptes()
    // {
    //     return $this->hasMany(SocieteAccomptesCommandes::class, 'id_commande', 'id_commande');
    // }
}
