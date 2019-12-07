<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasHarianArea extends Model
{
    protected $table = 'aktivitas_harian_area';
    protected $primaryKey = null;

    protected $fillable = [
        'id_aktivitas_harian',
        'id_area_stok',
        'jumlah',
        'created_at',
        'created_by',
        'tipe',
    ];

    protected $hidden = [
        // 'created_at',
        // 'created_by',
    ];

    protected $dates = ['created_at'];

    public $timestamps  = false;
    public $incrementing = false;

    public function areaStok()
    {
        return $this->belongsTo(AreaStok::class, 'id_area_stok');
    }
}
