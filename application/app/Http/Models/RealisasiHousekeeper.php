<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiHousekeeper extends Model
{
    protected $table = 'realisasi_housekeeper';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_realisasi',
        'id_tkbm',
        'id_area',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $timestamps  = false;

    public function areaHousekeeperFoto()
    {
        return $this->hasMany(AreaHousekeeperFoto::class, 'id_realisasi_housekeeper');
    }
}
