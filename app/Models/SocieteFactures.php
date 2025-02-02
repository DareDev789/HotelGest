<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocieteFactures extends Model
{

    protected $table = 'societe_factures';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_accompte',
        'date_facture',
        'user',
        'num_facture',
        'link',
        'id_hotel',
    ];

    public function hotel()
    {
        return $this->belongsTo(SocieteHotel::class, 'id_hotel', 'id_hotel');
    }

    public function accompte()
    {
        return $this->belongsTo(SocieteAccomptesReservations::class, 'id_accompte', 'id');
    }

    public function user()
    {
        return $this->belongsTo(SocieteUser::class, 'user', 'id');
    }
}
