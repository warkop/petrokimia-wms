<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PemetaanSloc extends Model
{
    protected $table = 'pemetaan_sloc';
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

    public function detailPemetaanSloc()
    {
        return $this->hasMany(DetailPemetaanSloc::class, 'id_pemetaan_sloc');
    }
}
