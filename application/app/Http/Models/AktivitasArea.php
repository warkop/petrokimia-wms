<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasArea extends Model
{
    protected $table = 'aktivitas_harian_area';
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
}
