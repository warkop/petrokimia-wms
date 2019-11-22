<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasKelayakanFoto extends Model
{
    protected $table = 'aktivitas_kelayakan_foto';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
    ];

    protected $dates = ['created_at'];

    public $timestamps  = false;
}
