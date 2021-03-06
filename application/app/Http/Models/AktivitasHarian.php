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
        return $this->belongsTo(Users::class, 'updated_by');
    }

    public function aktivitasFoto()
    {
        return $this->hasMany(AktivitasFoto::class, 'id_aktivitas_harian', 'id');
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

    public function karu()
    {
        return $this->belongsTo(Users::class, 'id_karu');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $sort = 'asc', $field = 'id', $condition)
    {
        $result = AktivitasHarian::
            select(
                'aktivitas_harian.id AS id', 
                'aktivitas.nama as nama_aktivitas',
                'aktivitas_harian.updated_at as tanggal', 
                'gudang.nama as nama_gudang', 
                'shift_kerja.nama as nama_shift',
                'aktivitas_harian.nopol',
                'aktivitas_harian.driver',
                'aktivitas_harian.posto',
                'shift_kerja.nama as nama_shift',
                'approve',
                'tenaga_kerja_non_organik.nama as checker'
            )
            ->join('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->join('gudang', 'gudang.id', '=', 'aktivitas_harian.id_gudang')
            ->join('shift_kerja', 'shift_kerja.id', '=', 'aktivitas_harian.id_shift')
            ->join('users', 'users.id', '=', 'aktivitas_harian.updated_by')
            ->join('tenaga_kerja_non_organik', 'tenaga_kerja_non_organik.id', '=', 'users.id_tkbm')
            ->join('material_trans', 'id_aktivitas_harian', '=', 'aktivitas_harian.id')
            ->join('material', 'material.id', '=', 'material_trans.id_material')
            ->where('draft', 0)
            ;

        if (isset($condition['produk'])) {
            $result = $result->with(['materialTrans.material' => function($query) use ($condition) {
                foreach ($condition['produk'] as $data) {
                    $query->orWhere('id', $data);
                }
            }]);

            $result->where(function($query) use($condition) {
                foreach ($condition['produk'] as $data) {
                    $query->orWhere('material_trans.id_material', $data);
                }
            });
        } else {
            $result = $result->with('materialTrans.material');
        }

        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(aktivitas.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $query->orwhere(DB::raw('LOWER(nopol)'), 'ILIKE', '%' . strtolower($search) . '%');
                $query->orwhere(DB::raw('LOWER(driver)'), 'ILIKE', '%' . strtolower($search) . '%');
                $query->orwhere(DB::raw('LOWER(posto)'), 'ILIKE', '%' . strtolower($search) . '%');
                $query->orWhere('gudang.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $query->orWhere('shift_kerja.nama', 'ILIKE', '%' . strtolower($search) . '%');
                $query->orWhere(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'DD-MM-YYYY HH24:MI')"), 'ILIKE', '%' . strtolower($search) . '%');
                $query->orWhere('material.nama', 'ilike', '%'. $search . '%');
                // $query->orWhere('material_trans.jumlah', $search);
            });
        }

        if (isset($condition['id_gudang'])) {
            $result = $result->where('id_gudang', $condition['id_gudang']);
        }
        
        if (isset($condition['id_shift'])) {
            $result = $result->where('id_shift', $condition['id_shift']);
        }

        if (isset($condition['start_date'])) {
            $result = $result->where("aktivitas_harian.updated_at", '>=', date('Y-m-d', strtotime($condition['start_date'])));
        }

        if (isset($condition['end_date'])) {
            $result = $result->where("aktivitas_harian.updated_at", '<=', date('Y-m-d', strtotime($condition['end_date'].'+1 day')));
        }

        $count      = $result->count();
        $result     = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();

        return ['result' => $result, 'count' => $count];
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
                $where->orWhere(DB::raw("TO_CHAR(aktivitas_harian.created_at, 'DD-MM-YYYY')"), 'ILIKE', '%' . strtolower($search) . '%');
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
