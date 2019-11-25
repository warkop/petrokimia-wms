<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiMaterial extends Model
{
    protected $table = 'realisasi_material';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $timestamps  = false;

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($table) {
            $table->updated_at = now();
        });

        static::creating(function ($table) {
            $table->created_at = now();
        });
    }
}
