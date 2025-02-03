<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class SocieteUser extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $fillable = [ 
        'username',
        'password',
        'email',
        'nom',
        'id_hotel',
        'niveau_user',
        'validated',
        'auth',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
