<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteSettingFactures extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'entete',
        'footer',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
