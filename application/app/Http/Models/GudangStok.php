<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GudangStok extends Model
{
    protected $table = 'gudang_stok';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public $timestamps  = true;

    public function scopeDipakai($query)
    {
        return $query->where('status', 2);
    }

    public function scopeKosong($query)
    {
        return $query->where('status', 3);
    }

    public function scopeRusak($query)
    {
        return $query->where('status', 4);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }

    public function materialTrans()
    {
        return $this->hasMany(MaterialTrans::class, 'id_gudang_stok', 'id');
    }

    public function gridJson($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition, $id_gudang)
    {
        $result = DB::table($this->table)
            ->select(
                'material_trans.id AS id',
                'tanggal',
                'nama as nama_material',
                'alasan',
                'material_trans.jumlah',
                'material_trans.tipe',
                'status_pallet',
                'upload_file'
            )
            ->Join('material', 'gudang_stok.id_material', '=', 'material.id')
            ->Join('material_trans', 'gudang_stok.id', '=', 'material_trans.id_gudang_stok')
            ->where('id_gudang', $id_gudang)
            ->where('id_adjustment', null)
            ->where('id_realisasi_material', null)
            ->where('id_aktivitas_harian', null);

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(alasan)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('material_trans.jumlah::varchar(255)'), 'ILIKE', '%'.strtolower($search).'%');
                $where->orWhere(DB::raw("TO_CHAR(material_trans.tanggal, 'DD-MM-YYYY')"), 'ILIKE', '%' . strtolower($search) . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}
