<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class HotUsers extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hot_users';
    public $timestamps = true;
    protected $primaryKey = 'ID';

    protected $fillable = [
        'username', 'password', 'email', 'nom_user', 'id_hotel', 'niveau_user', 'validated', 'auth',
    ];

    // Relation avec l'hôtel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'ID');
    }
}

