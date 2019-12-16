<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasHarian extends Model
{
    protected $table = 'aktivitas_harian';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        // 'created_at',
        // 'created_by',
    ];

    protected $dates = ['created_at'];

    public $timestamps  = false;

    public function aktivitas()
    {
        return $this->hasOne(Aktivitas::class, 'id', 'id_aktivitas');
    }

    public function gudang()
    {
        return $this->hasOne(Gudang::class, 'id', 'id_gudang');
    }
    
    public function gudangTujuan()
    {
        return $this->hasOne(Gudang::class, 'id', 'id_gudang_tujuan');
    }
    
    public function shift()
    {
        return $this->hasOne(ShiftKerja::class, 'id', 'id_shift');
    }

    public function alatBerat()
    {
        return $this->hasOne(AlatBerat::class, 'id', 'id_alat_berat');
    }
    
    public function checker()
    {
        return $this->belongsTo(TenagaKerjaNonOrganik::class, 'created_by');
    }

    public function aktivitasFoto()
    {
        return $this->hasManyThrough(AktivitasFoto::class, JenisFoto::class, 'id_aktivitas_harian', 'id_foto_jenis', 'id', 'id');
    }

    public function produk()
    {
        return $this->hasManyThrough(Material::class, MaterialTrans::class, 'id_aktivitas_harian', 'id', 'id', 'id_material');
    }

    public function notifications()
    {
        return $this->morphMany(Notifications::class, 'notifiable');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = \DB::table($this->table)
            ->select(
                'aktivitas_harian.id AS id', 
                'aktivitas.nama as nama_aktivitas',
                'aktivitas_harian.created_at as tanggal', 
                'gudang.nama as nama_gudang', 
                'shift_kerja.nama as nama_shift',
                'approve'
            )
            ->join('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->join('gudang', 'gudang.id', '=', 'aktivitas_harian.id_gudang')
            ->join('shift_kerja', 'shift_kerja.id', '=', 'aktivitas_harian.id_shift')
            ->whereNotNull('pengaruh_tgl_produksi')
            ;

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(aktivitas.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('gudang.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('shift_kerja.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw("TO_CHAR(aktivitas_harian.created_at, 'DD/MM/YYYY')"), 'ILIKE', '%' . strtolower($search) . '%');
            });
        }

        if (!$condition) {
            foreach ($condition as $key => $value) {
                $result = $result->where($key, $value);
            }
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
