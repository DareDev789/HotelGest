<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie_Menu extends Model
{

    protected $table = 'categorie_menu';
    protected $primaryKey = 'id_categorie_menu';
    public $incrementing = true;

    protected $fillable = [
        'nom_categorie_menu',
        'id_hotel',
    ];
}
