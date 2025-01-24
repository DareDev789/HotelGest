<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prestations extends Model
{

    protected $table = 'prestations';
    protected $primaryKey = 'ID';
    public $incrementing = true;

    protected $fillable = [
        'prestation',
        'prix_prestation',
        'autre_info_prestation',
        'nom_agence',
        'id_hotel',
        'etat',
    ];
}
