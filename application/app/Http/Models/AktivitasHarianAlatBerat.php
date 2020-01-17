<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasHarianAlatBerat extends Model
{
    protected $table = 'aktivitas_harian_alat_berat';
    protected $primaryKey = null;

    protected $guarded = [];

    protected $hidden = [];
    protected $dates = [];

    public $timestamps  = false;
    public $incrementing = false;
}
