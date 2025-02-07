<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteDepenses extends Model
{
    protected $table = 'societe_depenses';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'categorie',
        'montant',
        'description',
        'id_hotel',
        'save_by',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function user()
    {
        return $this->belongsTo(SocieteUser::class, 'save_by', 'id');
    }
}
