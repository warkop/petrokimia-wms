<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasHarian extends Model
{
    protected $table = 'aktivitas_harian';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        // 'created_at',
        // 'created_by',
    ];

    public $timestamps  = false;
}
