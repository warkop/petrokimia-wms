<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Sistro extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'DataTiket';
}
