<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeBungalow extends Model
{
    protected $table = 'typeBungalow';
    protected $primaryKey = 'ID';
    public $incrementing = true;

    protected $fillable = [
        'ID_bungalow',
        'type_bungalow',
        'prix_bungalow',
        'prixAgence',
        'id_hotel',
    ];
}
