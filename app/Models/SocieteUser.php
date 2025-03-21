<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class SocieteUser extends Model 
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $table = 'societe_users';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'email',
        'nom',
        'id_hotel',
        'niveau_user',
        'validated',
        'profil',
        'auth',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }
}
