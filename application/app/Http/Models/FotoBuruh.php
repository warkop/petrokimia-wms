<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FotoBuruh extends Model
{
    protected $table = 'foto_buruh';
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

    public $timestamps  = true;
}
