<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPemetaanSloc extends Model
{
    protected $table = 'detail_pemetaan_sloc';
    protected $primaryKey = null;

    protected $dates = ['created_at', 'updated_at'];

    public $incrementing = false;
}
