<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    protected $table = 'realisasi';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $timestamps  = false;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $table->updated_by = \Auth::id();
            $table->updated_at = now();
        });

        static::creating(function ($table) {
            $table->created_by = \Auth::id();
            $table->created_at = now();
        });
    }

    public function realisasiHousekeeper()
    {
        return $this->hasMany(RealisasiHousekeeper::class, 'id_realisasi');
    }

    public function areaHousekeeperFoto()
    {
        return $this->hasManyThrough(AreaHousekeeperFoto::class, RealisasiHousekeeper::class, 'id_realisasi', 'id_realisasi_housekeeper', 'id', 'id');
    }
}
