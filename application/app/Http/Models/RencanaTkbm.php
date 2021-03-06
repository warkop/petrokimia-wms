<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RencanaTkbm extends Model
{
    protected $table = 'rencana_tkbm';
    protected $primaryKey = null;

    protected $fillable = [
        'id_rencana',
        'id_tkbm',
        'tipe',
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

    public function scopeOperator($query)
    {
        return $query->where('tipe', 2);
    }

    public function tkbm()
    {
        return $this->belongsTo(TenagaKerjaNonOrganik::class, 'id_tkbm');
    }

    public function rencanaHarian()
    {
        return $this->belongsTo(RencanaHarian::class, 'id_rencana');
    }
}
