<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiHousekeeper extends Model
{
    protected $table = 'realisasi_housekeeper';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    public $timestamps  = false;

    public function areaHousekeeperFoto()
    {
        return $this->hasMany(AreaHousekeeperFoto::class, 'id_realisasi_housekeeper');
    }
}
