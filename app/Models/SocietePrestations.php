<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocietePrestations extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'prestation',
        'prix_prestation',
        'autre_info_prestation',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
