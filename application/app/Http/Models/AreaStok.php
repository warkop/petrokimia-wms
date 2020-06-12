<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AreaStok extends Model
{
    protected $table = 'area_stok';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at', 'tanggal'];

    public $timestamps  = true;

    public function area()
    {
        return $this->belongsTo('App\Http\Models\Area', 'id_area')->withoutGlobalScopes();
    }

    public function material()
    {
        return $this->belongsTo('App\Http\Models\Material', 'id_material');
    }

    public function materialTrans()
    {
        return $this->belongsTo(MaterialTrans::class, 'id', 'id_area_stok');
    }
}
