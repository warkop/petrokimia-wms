<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKerusakan extends Model
{
    protected $table = 'laporan_kerusakan';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
    ];

    protected $dates = ['created_at'];

    public $timestamps  = false;

    public function scopeIsPerbaikan($query)
    {
        return $query->where('jenis', 1)->exist;
    }

    public function scopeIsKeluhan($query)
    {
        return $query->where('jenis', 2);
    }
}
