<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiHousekeeper extends Model
{
    protected $table = 'realisasi_housekeeper';
    protected $primaryKey = 'id';

    protected $guard = [
        'id',
    ];
}
