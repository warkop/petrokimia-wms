<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Sap extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SD_GOODS_ISSUE';
}
