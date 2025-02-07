<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Macss extends Model
{
    protected $table = 'macss';

    protected $fillable = [
        'ID',
        'categorie',
        'background',
        'id_hotel',
    ];
}
