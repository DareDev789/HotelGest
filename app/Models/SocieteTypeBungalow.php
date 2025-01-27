<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteTypeBungalow extends Model
{
    protected $table = 'societe_type_bungalows';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_bungalow',
        'type_bungalow',
        'prix_particulier',
        'prix_agence',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function bungalow()
    {
        return $this->belongsTo(SocieteBungalow::class, 'id_bungalow', 'id');
    }
}
