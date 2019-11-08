<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKerusakanFoto extends Model
{
    protected $table = 'laporan_kerusakan_foto';
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

    protected $dates = ['created_at', 'updated_at'];

    public $timestamps  = false;
}
