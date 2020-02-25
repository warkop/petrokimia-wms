<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $table = 'version';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $dates = ['created_at', 'updated_at',];

    public $timestamps  = true;
}
