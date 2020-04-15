<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\AlatBeratKerusakan;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\MaterialTrans;
use App\Http\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        // $this->authorize('dashboard');
        $data['title'] = 'Dashboard';
        $laporan_kerusakan = [];

        $shift = ShiftKerja::get()->count();
        $shift1 = [];
        $shift2 = [];
        $shift3 = [];
        $komplain_gp_shift1 = [];
        $komplain_gp_shift2 = [];
        $komplain_gp_shift3 = [];

        for ($i=1; $i <= $shift; $i++) {
            for ($j=0; $j < 7; $j++) { 
                $temp = LaporanKerusakan::selectRaw('
                (SELECT COUNT(*) FROM laporan_kerusakan as s1 WHERE s1.jenis = 2 and id_shift='.$i.' and EXTRACT(DOW from s1.created_at)='.$j.') AS shift')
                ->distinct()
                ->first();
                
                if ($temp)
                    array_push(${'shift'.$i}, $temp);
            }
        }
        $data['shift1'] = $shift1;
        $data['shift2'] = $shift2;
        $data['shift3'] = $shift3;

        for ($i = 1; $i <= $shift; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $temp = AktivitasKeluhanGp::selectRaw('
                    (SELECT 
                        COUNT(*) 
                    FROM aktivitas_keluhan_gp as s1 
                    JOIN aktivitas_harian as ah ON ah.id = s1.id_aktivitas_harian 
                    WHERE draft = 0 and id_shift=' . $i . ' and EXTRACT(DOW from s1.created_at)=' . $j . ') AS shift')
                    ->distinct()
                    ->first();

                if ($temp)
                    array_push(${'komplain_gp_shift' . $i}, $temp);
            }
        }

        $data['komplain_gp_shift1'] = $komplain_gp_shift1;
        $data['komplain_gp_shift2'] = $komplain_gp_shift2;
        $data['komplain_gp_shift3'] = $komplain_gp_shift3;

        

        $data['gudang'] = Gudang::internal()->get();
        return view('dashboard.grid', $data);
    }

    public function getKeluhanAlatBerat()
    {
        $tanggal    = request()->input('tanggal');
        $shift      = request()->input('shift');
        $gudang     = request()->input('gudang');
        $data = [];

        $date = explode('-', $tanggal);
        

        // dd(date('Y-m-d', strtotime($date[1])));

        $queryShift = '';
        if ( $shift != null ) {
            $queryShift = ' and id_shift = '.$shift.' ';
        }

        $queryGudang = '';
        if ( $gudang != null ) {
            $queryGudang = ' and id_gudang = '.$gudang.' ';
        }
        
        $queryTanggal = '';
        if ( $tanggal != null ) {
            $queryTanggal = " and to_char(jam_rusak, 'YYYY-MM-DD') BETWEEN ".date('Y-m-d', strtotime($date[0]))." and ".date('Y-m-d', strtotime($date[1])).' ';
        }

        // $alatBeratKerusakan = AlatBeratKerusakan::get();
        // $panjang = count($alatBeratKerusakan);

        $alatBeratKerusakan = AlatBeratKerusakan::select(
            'nama',
            DB::raw("
            (select
                count(*)
            from
                laporan_kerusakan
            where
                id_kerusakan = alat_berat_kerusakan.id
            $queryShift
            $queryGudang
            and to_char(jam_rusak, 'YYYY-MM-DD') BETWEEN '2020-02-21' and '2020-02-28') as jumlah")
        )
        ->get();

        for ($j=0; $j < count($alatBeratKerusakan); $j++) { 
            
            $temp = [
                $alatBeratKerusakan[$j]->nama,
                (double)$alatBeratKerusakan[$j]->jumlah,
            ];

            array_push($data, $temp);
        }
        
        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getJumlahPallet()
    {
        $tanggal    = request()->input('tanggal');
        $shift      = request()->input('shift');
        $gudang     = request()->input('gudang');

        $date = explode('-', $tanggal);

        $tgl_awal = date('Y-m-d', strtotime($date[0]));
        $tgl_akhir = date('Y-m-d', strtotime($date[1]));

        $res = GudangStok::distinct()
        ->select('id_gudang')
        ->with('gudang')
        ->whereHas('gudang', function($query) use($gudang) {
            $query->where('tipe_gudang', $gudang);
        })
        ->whereHas('material', function ($query) {
            $query->where('kategori', 2);
        })
        ->orderBy('id_gudang', 'asc')
        ->get();

        $tempJumlahPallet[0] = 0;
        $tempJumlahPallet[1] = 0;
        $tempJumlahPallet[2] = 0;

        $data = [
           
        ];

        // array_push($data,  ['Gudang', 'Pakai & Dasaran', 'Kosong ', 'Rusak', 'Total Stok']);

        foreach ($res as $value) {
            for ($i=0; $i<3; $i++) {
                $masuk      = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use ($tgl_awal, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0)
                    ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                    ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')))
                    ;
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('gudang_stok', function ($join) {
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                // ->where('material_trans.id_material', $value->id_material)
                ->where('tipe', 2)
                ->where('id_shift', $shift)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');

                $keluar     = MaterialTrans::
                leftJoin('aktivitas_harian', function($join) use($tgl_awal, $value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                })
                ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $value){
                    $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ->where('material_adjustment.tanggal', '<', date('Y-m-d', strtotime($tgl_awal)));
                })
                ->leftJoin('gudang_stok', function ($join){
                    $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                })
                // ->where('material_trans.id_material', $value->id_material)
                ->where('tipe', 1)
                ->where('id_shift', $shift)
                ->where('gudang_stok.id_gudang', $value->id_gudang)
                ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                ->sum('material_trans.jumlah');
                $saldoAwal  = $masuk - $keluar;

                $peralihanTambah = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ;
                    })
                    ->leftJoin('material_adjustment', function($join) use($value){
                        $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ;
                    })
                    ->where(function($query) use($tgl_awal, $tgl_akhir){
                        $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                        $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                    })
                    ->where('tipe', 2)
                    ->where('status_pallet', ($i + 2))
                    ->where('id_shift', $shift)
                    // ->where('id_material', $value->id_material)
                    ->sum('jumlah');

                $peralihanKurang = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $value->id_gudang)
                        ;
                    })
                    ->leftJoin('material_adjustment', function($join) use($value) {
                        $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $value->id_gudang)
                        ;
                    })
                    ->where(function($query) use($tgl_awal, $tgl_akhir){
                        $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                        $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                    })
                    ->where('tipe', 1)
                    ->where('status_pallet', ($i + 2))
                    ->where('id_shift', $shift)
                    // ->where('id_material', $value->id_material)
                    ->sum('jumlah');
                

                $tempJumlahPallet[$i] = $saldoAwal+$peralihanTambah-$peralihanKurang;
            }

            $temp = [
                $value->gudang->nama, 
                $tempJumlahPallet[0],
                $tempJumlahPallet[1],
                $tempJumlahPallet[2],
                ($tempJumlahPallet[0]+$tempJumlahPallet[1]+$tempJumlahPallet[2]),
            ];

            array_push($data, $temp);
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getProduksiPengeluaran()
    {
        $tanggal    = request()->input('tanggal');
        $shift      = request()->input('shift');
        $gudang     = request()->input('gudang');

        $date = explode('-', $tanggal);

        $tgl_awal = date('Y-m-d', strtotime($date[0]));
        $tgl_akhir = date('Y-m-d', strtotime($date[1].'+1 day'));

        $data = [];
        $temp_tgl = $tgl_awal;
        do {
            $resKeluar = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0);
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
            ->whereNotNull('status_produk')
            ->where('tipe', 1)
            ->where(function($query) use ($gudang){
                $query->where('aktivitas_harian.id_gudang', $gudang);
                $query->orWhere('material_adjustment.id_gudang', $gudang);
            })
            ->where(function($query) use($temp_tgl){
                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl);
                $query->orWhere('material_adjustment.tanggal', $temp_tgl);
            })
            ->sum('material_trans.jumlah')
            ;
    
            $resMasuk = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ->where('draft', 0);
            })
            ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
            ->whereNotNull('status_produk')
            ->where('tipe', 2)
            ->where(function($query) use ($gudang){
                $query->where('aktivitas_harian.id_gudang', $gudang);
                $query->orWhere('material_adjustment.id_gudang', $gudang);
            })
            ->where(function($query) use($temp_tgl){
                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl);
                $query->orWhere('material_adjustment.tanggal', $temp_tgl);
            })
            ->sum('material_trans.jumlah')
            ;

            $temp[0] = $temp_tgl;
            $temp[1] = (double)$resMasuk;
            $temp[2] = (double)$resKeluar;

            array_push($data, $temp);

            $temp_tgl = date('Y-m-d', strtotime($temp_tgl.'+1 day'));
        } while ($temp_tgl != $tgl_akhir);
        
        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function map()
    {
        $data['title'] = 'Map Click';
        return view('dashboard.mapclick', $data);
    }
}