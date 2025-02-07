<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depenses extends Model
{
    protected $table = 'depense_global';

    protected $fillable = [
        'ID',
        'categorie',
        'montant',
        'date_save',
        'description',
        'save_by',
        'id_hotel',
    ];
}
