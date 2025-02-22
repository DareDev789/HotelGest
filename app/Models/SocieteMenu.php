<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteMenu extends Model
{
    use SoftDeletes;

    protected $table = 'societe_menu';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_categorie',
        'nom_menu',
        'prix_menu',
        'autres_info_menu',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }
    public function categorie()
    {
        return $this->belongsTo(SocieteCategorieMenu::class, 'id_categorie', 'id');
    }
    public function commandes()
    {
        return $this->hasMany(SocieteDetailsCommandesMenus::class, 'id_menu', 'id');
    }
}
