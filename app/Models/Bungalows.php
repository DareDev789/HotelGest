<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bungalows extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bungalows';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'designation_bungalow',
        'type_bungalow',
        'num_bungalow',
        'autres_info_bungalow',
        'prix_bungalow',
        'id_hotel',
        'etat_bungalow',
        'tri',
    ];

    protected $casts = [
        'prix_bungalow' => 'float',
        'etat_bungalow' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
    }

    public function getPrixBungalowAttribute($value)
    {
        return number_format($value, 2, '.', ' ');
    }
}
