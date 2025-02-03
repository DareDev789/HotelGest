<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id_menu';
    public $incrementing = true;

    protected $fillable = [
        'nom_menu',
        'prix_menu',
        'autres_info_menu',
        'id_categorie_menu',
        'etat',
    ];
}
