<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteBungalow extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'designation_bungalow',
        'type_bungalow',
        'num_bungalow',
        'id_hotel',
        'tri',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function detailsReservations()
    {
        return $this->hasMany(SocieteDetailsReservation::class, 'id_bungalow', 'id');
    }

    public function typeBungalow()
    {
        return $this->hasMany(SocieteTypeBungalow::class, 'id_bungalow', 'id');
    }
}
