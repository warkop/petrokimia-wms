<?php

namespace App\Http\Models;

use App\Scopes\EndDateScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AktivitasHarian extends Model
{
    use Notifiable;
    protected $table = 'aktivitas_harian';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $hidden = [];

    protected $dates = ['created_at', 'updated_at'];

    public $timestamps  = true;

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

    public function aktivitasHarianArea()
    {
        return $this->hasMany(AktivitasHarianArea::class, 'id_aktivitas_harian', 'id');
    }

    public function materialTrans()
    {
        return $this->hasMany(MaterialTrans::class, 'id_aktivitas_harian', 'id');
    }

    public function aktivitasKeluhanGp()
    {
        return $this->hasMany(AktivitasKeluhanGp::class, 'id_aktivitas_harian', 'id');
    }

    public function aktivitasHarianAlatBerat()
    {
        return $this->belongsToMany(AlatBerat::class, 'aktivitas_harian_alat_berat', 'id_aktivitas_harian', 'id_alat_berat');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table($this->table)
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
            // ->whereNotNull('pengaruh_tgl_produksi')
            ;

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(aktivitas.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('gudang.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('shift_kerja.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw("TO_CHAR(aktivitas_harian.created_at, 'DD/MM/YYYY')"), 'ILIKE', '%' . strtolower($search) . '%');
            });
        }

        if (!empty($condition)) {
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

    public function jsonGridGp($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition)
    {
        $result = DB::table($this->table)
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
            ->whereNotNull('pengiriman');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(aktivitas.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('gudang.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('shift_kerja.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw("TO_CHAR(aktivitas_harian.created_at, 'DD/MM/YYYY')"), 'ILIKE', '%' . strtolower($search) . '%');
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
