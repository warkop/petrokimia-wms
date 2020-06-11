<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;

class Area extends CustomModel
{
    protected $table = 'area';
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

    public function gudang()
    {
        return $this->hasOne(Gudang::class, 'id', 'id_gudang')->withoutGlobalScopes();
    }

    public function areaStok()
    {
        return $this->hasMany(AreaStok::class, 'id_area', 'id');
    }

    public function jsonGrid($start = 0, $length = 10, $search = '', $count = false, $sort = 'asc', $field = 'id', $condition, $id_gudang)
    {
        $result = DB::table($this->table)
            ->select('id AS id', 'nama AS nama', 'kapasitas', 'tipe')
            ->where('id_gudang', $id_gudang)
            ->whereNull('end_date');

        if (!empty($search)) {
            $result = $result->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('kapasitas::text'), 'ILIKE', '%' . $search . '%');
            });
        }

        if ($count == true) {
            $result = $result->count();
        } else {
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
    /*public function getStokArea($gudang, $produk, $pilih_produk, $tgl){
        $res = DB::table($this->table)
                ->select('area.id as id_area', 'gdg.nama as nama_gdg', 'area.nama as nama_area', 'stok.id_material', 'stok.total', 'area.kapasitas')
                ->join(DB::raw("(SELECT 
                        ar.id_gudang, 
                        ars.id_area, 
                        trans.id_material,
                        sum(case when trans.tipe = 1 then -trans.jumlah::NUMERIC else trans.jumlah::NUMERIC end) as total
                    FROM 
                        public.area_stok as ars 
                    join area ar on ar.id = ars.id_area
                    join material_trans as trans on trans.id_area_stok = ars.id
                    where trans.tanggal <= '{$tgl}'
                    group by ar.id_gudang, 
                        ars.id_area, 
                        trans.id_material
                    order by ar.id_gudang, 
                        ars.id_area) stok"),"stok.id_area","=","area.id")
                ->leftJoin("gudang as gdg", "gdg.id", "=", "area.id_gudang")
                ->orderBy(DB::raw("gdg.nama, area.nama"),'asc');
        return $res->get();
    }*/
    public function getStokGudang($gudang, $produk, $pilih_produk, $tgl){
        $res = DB::table("gudang as gdg")
                ->select('gdg.id as id_gudang', 'gdg.nama as nama_gudang', 'stok.id_material', 'stok.total', 'stok.kapasitas')
                ->leftjoin(DB::raw("(SELECT 
                        ar.id_gudang, 
                        trans.id_material,
                        sum(ar.kapasitas) as kapasitas,
                        sum(case when trans.tipe = 1 then -trans.jumlah::NUMERIC else trans.jumlah::NUMERIC end) as total
                    FROM 
                        area as ar 
                    left join area_stok ars on ars.id_area = ar.id
                    join material_trans as trans on trans.id_area_stok = ars.id
                    where trans.created_at <= '{$tgl}'
                    group by ar.id_gudang, trans.id_material
                    order by ar.id_gudang) stok"),"stok.id_gudang","=","gdg.id");
        if($gudang != ''){
            $res = $res->where(function ($where) use ($gudang) {
                $i = 0;
                foreach($gudang as $row){
                    if($i == 0)
                        $where->where('gdg.id',$row);
                    else
                        $where->orWhere('gdg.id',$row);
                    $i++;
                }
            });
        }
        $res = $res->where('gdg.tipe_gudang',1);
        $res = $res->orderBy(DB::raw("gdg.nama"),'asc');

        if ($produk == 2) {
            $res = $res->where(function ($query) use ($pilih_produk) {
                $query->where('stok.id_material', $pilih_produk[0]);
                foreach ($pilih_produk as $key => $value) {
                    $query = $query->orWhere('stok.id_material', $value);
                }
            });
        }

        return $res->get();
    }
    public function getProduk($gudang, $produk, $pilih_produk, $tgl){
        $res = DB::table("gudang as gdg")
                ->select(DB::raw('DISTINCT mat.id as id_material, mat.nama'))
                ->leftjoin(DB::raw("(SELECT 
                        ar.id_gudang, 
                        trans.id_material,
                        sum(ar.kapasitas) as kapasitas,
                        sum(case when trans.tipe = 1 then -trans.jumlah::NUMERIC else trans.jumlah::NUMERIC end) as total
                    FROM 
                        area as ar 
                    left join area_stok ars on ars.id_area = ar.id
                    join material_trans as trans on trans.id_area_stok = ars.id
                    where trans.created_at <= '{$tgl}'
                    group by ar.id_gudang, trans.id_material
                    order by ar.id_gudang) stok"),"stok.id_gudang","=","gdg.id");
        if($gudang != ''){
            $res = $res->where(function ($where) use ($gudang) {
                $i = 0;
                foreach($gudang as $row){
                    if($i == 0)
                        $where->where('gdg.id',$row);
                    else
                        $where->orWhere('gdg.id',$row);
                    $i++;
                }
            });
        }
        $res = $res->where('gdg.tipe_gudang',1);
        $res = $res->leftJoin('material as mat','mat.id','=','stok.id_material');

        if ($produk == 2) {
            $res = $res->where(function($query) use($pilih_produk){
                $query->where('stok.id_material', $pilih_produk[0]);
                foreach ($pilih_produk as $key => $value) {
                    $query = $query->orWhere('stok.id_material', $value);
                }
            });
        }

        return $res->get();
    }
}
