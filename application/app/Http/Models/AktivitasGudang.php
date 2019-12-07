<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasGudang extends Model
{
    protected $table = 'aktivitas_gudang';
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

    public function aktivitas()
    {
        return $this->belongsTo(Aktivitas::class, 'id_aktivitas');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }
}
