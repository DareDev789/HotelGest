<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accomptes extends Model
{
    protected $table = 'accomptes';

    protected $fillable = [
        'ID',
        'ident_reservation',
        'montant',
        'date_save',
        'save_by',
        'id_hotel',
    ];
}
