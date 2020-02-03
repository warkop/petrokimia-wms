<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class KeluhanOperator extends Model
{
    protected $table = 'keluhan_operator';
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

    protected $dates = [ 'end_date', 'created_at', 'updated_at'];

    public $timestamps  = false;
}
