<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasKeluhanGp extends Model
{
    protected $table = 'aktivitas_keluhan_gp';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
        'created_at',
        'created_by',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
    ];

    protected $dates = ['created_at'];

    public $timestamps  = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = auth()->id();
            $table->created_at = now();
        });
    }

    public function aktivitasHarian()
    {
        return $this->belongsTo(AktivitasHarian::class, 'id_aktivitas_harian');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }
}
