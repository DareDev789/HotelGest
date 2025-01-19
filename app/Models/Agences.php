<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agences extends Model
{

    protected $table = 'agence';
    protected $primaryKey = 'ID';
    public $incrementing = true;

    protected $fillable = [
        'email_agence',
        'telephone_agence',
        'site_web_agence',
        'nom_agence',
        'autres_info_agence',
        'id_hotel',
        'color',
    ];
}
