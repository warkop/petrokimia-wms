<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GudangStok extends Model
{
    protected $table = 'gudang_stok';
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

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

    public $timestamps  = false;

    public function gridJson($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition, $id_gudang)
    {
        $result = \DB::table($this->table)
            ->select(
                'material_trans.id AS id',
                'tanggal',
                'nama as nama_material',
                'alasan',
                'material_trans.jumlah',
                'material_trans.tipe',
                'status_pallet'
            )
            ->Join('material', 'gudang_stok.id_material', '=', 'material.id')
            ->Join('material_trans', 'material_trans.id_material', '=', 'gudang_stok.id_material')
            ->where('id_gudang', $id_gudang)
            ->where('id_adjustment', null)
            ->where('id_realisasi_material', null)
            ->where('id_aktivitas_harian', null);

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('LOWER(alasan)', 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere('jumlah', 'ILIKE', '%' . strtolower($search) . '%');
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
