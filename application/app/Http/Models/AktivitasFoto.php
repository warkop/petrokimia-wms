<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasFoto extends Model
{
    protected $table = 'aktivitas_foto';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

    public $timestamps  = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            // $table->created_by = \Auth::id();
            $table->created_at = now();
        });
    }

    public function fotoJenis()
    {
        return $this->belongsTo(JenisFoto::class, 'id_foto_jenis', 'id');
    }
}
