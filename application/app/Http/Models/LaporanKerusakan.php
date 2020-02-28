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

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'id_alat_berat');
    }

    public function kerusakan()
    {
        return $this->belongsTo(AlatBeratKerusakan::class, 'id_kerusakan');
    }

    public function shift()
    {
        return $this->belongsTo(ShiftKerja::class, 'id_shift');
    }

    public function operator()
    {
        return $this->belongsTo(TenagaKerjaNonOrganik::class, 'id_operator');
    }

    public function foto()
    {
        return $this->hasMany(LaporanKerusakanFoto::class, 'id_laporan');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }
}
