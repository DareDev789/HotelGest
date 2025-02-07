<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteCategorieMenu extends Model
{
    use SoftDeletes;
    protected $table = 'societe_categorie_menu';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'nom_categorie_menu',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }
    public function Menu()
    {
        return $this->hasMany(SocieteMenu::class, 'id_categorie', 'id');
    }
}
