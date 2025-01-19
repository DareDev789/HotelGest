<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clients extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id_client';
    public $incrementing = true;

    protected $fillable = [
        'nom_client',
        'adresse',
        'email',
        'telephone',
        'Id_hotel',
        'autres_info_client',
    ];
}
