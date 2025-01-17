<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class HotUsers extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'hot_users';
    public $timestamps = true;
    protected $primaryKey = 'ID';

    protected $fillable = [
        'username', 'password', 'email', 'nom_user', 'id_hotel', 'niveau_user', 'validated', 'auth',
    ];

    protected $hidden = [
        'password', 'remember_token', 
    ];

    // Relation avec l'hôtel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }
}
