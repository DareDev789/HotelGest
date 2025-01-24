<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteServicesDivers extends Model
{
    use SoftDeletes;

    protected $table = 'societe_services_divers';

    protected $fillable = [
        'id',
        'designation',
        'description',
        'id_hotel',
        'prixPax',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
