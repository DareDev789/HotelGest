<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteAgence extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email_agence',
        'telephone_agence',
        'site_web_agence',
        'nom_agence',
        'id_hotel',
        'autres_info_agence',
        'bg_color',
        'text_color',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function reservations()
    {
        return $this->hasMany(SocieteReservation::class, 'id_agence', 'id');
    }
}
