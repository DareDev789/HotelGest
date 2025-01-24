<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDevise extends Model
{
    use SoftDeletes;

    protected $table = 'societe_devise';

    protected $fillable = [
        'id',
        'type',
        'devise',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
