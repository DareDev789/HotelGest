<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicesDivers extends Model
{

    protected $table = 'divers';

    protected $fillable = [
        'ID',
        'designation',
        'prix_jour',
        'description',
        'id_hotel',
        'etat',
    ];
}
