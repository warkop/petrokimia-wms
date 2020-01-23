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

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'id_alat_berat');
    }

    public function aktivitasHarian()
    {
        return $this->belongsTo(aktivitasHarian::class, 'id_aktivitas_harian');
    }
}
