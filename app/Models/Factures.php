<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factures extends Model
{

    protected $table = 'factures';
    protected $primaryKey = 'id_facture';
    public $incrementing = true;

    protected $fillable = [
        'ident_reservation',
        'date_facture',
        'id_hotel',
        'user',
        'num_facture',
        'link',
    ];

    
}
