<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaAreaTkbm extends Model
{
    protected $table = 'rencana_area_tkbm';
    protected $primaryKey = null;
    

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
    
    public $incrementing = false;

    public function tkbm()
    {
        return $this->hasMany('App\Http\Models\TenagaKerjaNonOrganik', 'id_tkbm', 'id');
    }
}
