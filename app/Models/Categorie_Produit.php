<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie_Produit extends Model
{

    protected $table = 'categorie_produit';
    protected $primaryKey = 'ID';
    public $incrementing = true;

    protected $fillable = [
        'nom_categorie',
        'id_hotel',
    ];
}
