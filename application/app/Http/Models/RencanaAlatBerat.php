<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaAlatBerat extends Model
{
    protected $table = 'rencana_alat_berat';
    protected $primaryKey = 'id_rencana';

    protected $fillable = [
        'id_rencana',
        'id_alat_berat',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

    public $timestamps  = false;
}
