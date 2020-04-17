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
use App\Http\Models\HandlingPerJenisProduk;
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
        $tgl_akhir = date('Y-m-d', strtotime($date[1].'+1 day'));

        $res = GudangStok::distinct()
        ->select('id_gudang')
        ->with('gudang')
        ->whereHas('gudang', function($query) use($gudang) {
            $query->where('id', $gudang);
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
                if ($shift == 1) {
                    $saldoAwal = 0;
                    $pre_masuk     = MaterialTrans::
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
                    ->where(function ($query) use ($tgl_awal) {
                        $query->where(function($query) use($tgl_awal){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 07:00:00')));
                            $query->orWhere(function($query) use($tgl_awal){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 07:00:00')));
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 15:00:00')));
                                $query->where('id_shift', 3);
                            });
                        });

                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_adjustment.tanggal', '<', $tgl_awal);
                            $query->orWhere(function($query) use ($tgl_awal){
                                $query->where('material_adjustment.tanggal', '=', $tgl_awal);
                                $query->where('material_adjustment.shift', '=', 3);
                            });
                        });
                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_trans.tanggal', '<', $tgl_awal);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                        });
                    })
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ->where('tipe', 2)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->sum('material_trans.jumlah');

                    $pre_keluar     = MaterialTrans::
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
                    ->where(function ($query) use ($tgl_awal) {
                        $query->where(function($query) use($tgl_awal){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 07:00:00')));
                            $query->orWhere(function($query) use($tgl_awal){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 07:00:00')));
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 15:00:00')));
                                $query->where('id_shift', 3);
                            });
                        });

                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_adjustment.tanggal', '<', $tgl_awal);
                            $query->orWhere(function($query) use ($tgl_awal){
                                $query->where('material_adjustment.tanggal', '=', $tgl_awal);
                                $query->where('material_adjustment.shift', '=', 3);
                            });
                        });
                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_trans.tanggal', '<', $tgl_awal);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                        });
                    })
                    ->where('tipe', 1)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->sum('material_trans.jumlah');

                    $saldoAwal = $pre_masuk - $pre_keluar;
                } else if ($shift == 2) {
                    $saldoAwal = 0;
                    $pre_masuk     = MaterialTrans::
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
                    ->where(function ($query) use ($tgl_awal) {
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 15:00:00')));
                        $query->orWhere(function($query) use($tgl_awal){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 15:00:00')));
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00')));
                            $query->where('id_shift', 1);
                        });
                        $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
                        $query->orWhere(function ($query) use ($tgl_awal) {
                                $query->where('material_adjustment.tanggal', '=', $tgl_awal);
                                $query->where(function($query){
                                    $query->where('material_trans.shift_id', 1);
                                    $query->orWhere('material_trans.shift_id', 3);
                                });
                        });
                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_trans.tanggal', '<', $tgl_awal);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                        });
                    })
                    ->where('tipe', 2)
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->sum('material_trans.jumlah');

                    $pre_keluar     = MaterialTrans::
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
                    ->where(function ($query) use ($tgl_awal) {
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 15:00:00')));
                        $query->orWhere(function($query) use($tgl_awal){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 15:00:00')));
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00')));
                            $query->where('id_shift', 1);
                        });
                        $query->orWhere('material_adjustment.tanggal', '<', $tgl_awal);
                        $query->orWhere(function ($query) use ($tgl_awal) {
                                $query->where('material_adjustment.tanggal', '=', $tgl_awal);
                                $query->where(function($query){
                                    $query->where('material_trans.shift_id', 1);
                                    $query->orWhere('material_trans.shift_id', 3);
                                });
                        });
                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_trans.tanggal', '<', $tgl_awal);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                        });
                    })
                    ->where('tipe', 1)
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->sum('material_trans.jumlah');

                    $saldoAwal = $pre_masuk - $pre_keluar;
                } else if ($shift == 3) {
                    $pre_masuk = MaterialTrans::
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
                    ->where(function ($query) use ($tgl_awal) {
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                        $query->orWhere(function($query) use($tgl_awal){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:30:00')));
                            $query->where('id_shift', 2);
                        });
                        // $query->orWhere('material_adjustment.tanggal', '<=', date('Y-m-d', strtotime($tgl_awal . '-1 day')));
                        $query->orWhere('material_adjustment.tanggal', '<=', date('Y-m-d', strtotime($tgl_awal . '-1 day')));
                        $query->orWhere(function ($query) use ($tgl_awal) {
                                $query->where('material_adjustment.tanggal', '=', date('Y-m-d', strtotime($tgl_awal . '-1 day')));
                                $query->where(function($query){
                                    $query->where('material_trans.shift_id', 2);
                                    $query->orWhere('material_trans.shift_id', 1);
                                });
                        });
                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_trans.tanggal', '<', $tgl_awal);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                        });
                    })
                    ->where('tipe', 2)
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->sum('material_trans.jumlah');

                    $pre_keluar = MaterialTrans::
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
                    ->where(function ($query) use ($tgl_awal) {
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                        $query->orWhere(function($query) use($tgl_awal){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '>=', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')));
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), '<', date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:30:00')));
                            $query->where('id_shift', 2);
                        });
                        $query->orWhere('material_adjustment.tanggal', '<=', date('Y-m-d', strtotime($tgl_awal . '-1 day')));
                        $query->orWhere(function ($query) use ($tgl_awal) {
                                $query->where('material_adjustment.tanggal', '=', date('Y-m-d', strtotime($tgl_awal . '-1 day')));
                                $query->where(function($query){
                                    $query->where('material_trans.shift_id', 2);
                                    $query->orWhere('material_trans.shift_id', 1);
                                });
                        });
                        $query->orWhere(function ($query) use ($tgl_awal) {
                            $query->where('material_trans.tanggal', '<', $tgl_awal);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                        });
                    })
                    ->where('tipe', 1)
                    ->where('gudang_stok.id_gudang', $value->id_gudang)
                    ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                    ->sum('material_trans.jumlah');

                    $saldoAwal = $pre_masuk - $pre_keluar;
                }

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
                    ->leftJoin('gudang_stok', function($join) use($value) {
                        $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
                        ->where('gudang_stok.id_gudang', $value->id_gudang)
                        ;
                    })
                    ->where(function($query) use($tgl_awal, $tgl_akhir, $shift){
                        $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                        $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                        $query->orWhere(function ($query) use ($tgl_awal, $tgl_akhir, $shift) {
                            $query->whereBetween('material_trans.tanggal', [$tgl_awal, $tgl_akhir]);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                            $query->where('material_trans.shift_id', $shift);
                        });
                    })
                    ->where('tipe', 2)
                    ->where('status_pallet', ($i + 2))
                    ->where(function($query) use($shift) {
                        $query->where('id_shift', $shift);
                        $query->orWhere('shift', $shift);
                    })
                    ->sum('material_trans.jumlah');

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
                    ->leftJoin('gudang_stok', function($join) use($value) {
                        $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
                        ->where('gudang_stok.id_gudang', $value->id_gudang)
                        ;
                    })
                    ->where(function($query) use($tgl_awal, $tgl_akhir, $shift){
                        $query->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [date('Y-m-d H:i:s', strtotime($tgl_awal . ' 23:00:00 -1 day')), date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:00:00 -1 day'))]);
                        $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                        $query->orWhere(function ($query) use ($tgl_awal, $tgl_akhir, $shift) {
                            $query->whereBetween('material_trans.tanggal', [$tgl_awal, $tgl_akhir]);
                            $query->whereNull('material_trans.id_aktivitas_harian');
                            $query->whereNull('material_trans.id_adjustment');
                            $query->where('material_trans.shift_id', $shift);
                        });
                    })
                    ->where('tipe', 1)
                    ->where('status_pallet', ($i + 2))
                    ->where(function($query) use($shift) {
                        $query->where('id_shift', $shift);
                        $query->orWhere('shift', $shift);
                    })
                    ->sum('material_trans.jumlah');

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
        $this->responseData = [$data, date('d/m/Y', strtotime($tgl_awal)), date('d/m/Y', strtotime($tgl_akhir.'-1 day'))];

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
            ->where(function($query) use($shift) {
                $query->where('aktivitas_harian.id_shift', $shift);
                $query->orWhere('material_adjustment.shift', $shift);
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
            ->where(function($query) use($shift) {
                $query->where('aktivitas_harian.id_shift', $shift);
                $query->orWhere('material_adjustment.shift', $shift);
            })
            ->sum('material_trans.jumlah')
            ;

            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
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

    public function getPemuatanProduk()
    {
        $tanggal    = request()->input('tanggal');
        $shift      = request()->input('shift');
        $gudang     = request()->input('gudang');

        $date = explode('-', $tanggal);

        $tgl_awal = date('Y-m-d', strtotime($date[0]));
        $tgl_akhir = date('Y-m-d', strtotime($date[1].'+1 day'));

        $temp_tgl = $tgl_awal;

        $data = [];
        do {
            if ($shift == 3) {
                $kapasitas = DB::table('realisasi')
                ->leftJoin('rencana_harian', 'rencana_harian.id', '=', 'realisasi.id_rencana')
                ->where('id_gudang', $gudang)
                ->where('id_shift', $shift)
                ->where('rencana_harian.tanggal', date('Y-m-d', strtotime($temp_tgl.'-1 day')))
                ->first()
                ;

                $angkut = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0);
                })
                ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                ->whereNotNull('status_produk')
                ->where(function($query) use ($gudang){
                    $query->where('aktivitas_harian.id_gudang', $gudang);
                    $query->orWhere('material_adjustment.id_gudang', $gudang);
                })
                ->where(function($query) use($temp_tgl){
                    $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl.'-1 day')));
                    $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                })
                ->whereNotNull('butuh_tkbm')
                ->where('id_shift', $shift)
                ->sum('material_trans.jumlah')
                ;
            } else {
                $kapasitas = DB::table('realisasi')
                ->leftJoin('rencana_harian', 'rencana_harian.id', '=', 'realisasi.id_rencana')
                ->where('id_gudang', $gudang)
                ->where('id_shift', $shift)
                ->where('rencana_harian.tanggal', $temp_tgl)
                ->first()
                ;

                $angkut = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                    ->where('draft', 0);
                })
                ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                ->whereNotNull('status_produk')
                ->where(function($query) use ($gudang){
                    $query->where('aktivitas_harian.id_gudang', $gudang);
                    $query->orWhere('material_adjustment.id_gudang', $gudang);
                })
                ->where(function($query) use($temp_tgl){
                    $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl);
                    $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                })
                ->whereNotNull('butuh_tkbm')
                ->where('id_shift', $shift)
                ->sum('material_trans.jumlah')
                ;
            }

            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $temp[1] = ($kapasitas->jumlah_buruh??0)*60;
            $temp[2] = (double)$angkut;

            array_push($data, $temp);

            $temp_tgl = date('Y-m-d', strtotime($temp_tgl.'+1 day'));
        } while ($temp_tgl != $tgl_akhir);

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getTonaseProdukRusak()
    {
        $tanggal    = request()->input('tanggal');
        $shift      = request()->input('shift');
        $pilih_gudang     = request()->input('gudang');

        $date = explode('-', $tanggal);
        $tgl_awal = date('Y-m-d', strtotime($date[0]));
        $tgl_akhir = date('Y-m-d', strtotime($date[1].'+1 day'));

        $temp_tgl = $tgl_awal;

        if ($pilih_gudang) {
            $gudang = Gudang::where('id', $pilih_gudang)->get();
        } else {
            $gudang = Gudang::internal()->get();
        }
        $data = [];
        do {
            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $i = 1;
            if ($pilih_gudang) {
                if ($shift == 3) {
                    $res = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($pilih_gudang){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $pilih_gudang)
                        ;
                    })
                    ->leftJoin('material_adjustment', function($join) use($pilih_gudang) {
                        $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $pilih_gudang)
                        ;
                    })
                    ->where(function($query) use($temp_tgl){
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl)));
                        $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                    })
                    ->where('tipe', 2)
                    ->where('id_shift', $shift)
                    ->where('status_produk', 2)
                    ->sum('jumlah');
                } else {
                    $res = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($pilih_gudang){
                    $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $pilih_gudang)
                        ;
                    })
                    ->leftJoin('material_adjustment', function($join) use($pilih_gudang) {
                        $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                        ->where('material_adjustment.id_gudang', $pilih_gudang)
                        ;
                    })
                    ->where(function($query) use($temp_tgl){
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl)));
                        $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                    })
                    ->where('tipe', 2)
                    ->where('id_shift', $shift)
                    ->where('status_produk', 2)
                    ->sum('jumlah');
                }

                $temp[$i] = (double)$res;
            } else {
                foreach ($gudang as $value) {
                    if ($shift == 3) {
                        $res = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
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
                            ->where(function($query) use($temp_tgl){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl.'-1 day')));
                                $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                            })
                            ->where('tipe', 2)
                            ->where('id_shift', $shift)
                            ->where('status_produk', 2)
                            ->sum('jumlah');
                    } else {
                        $res = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($value){
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
                            ->where(function($query) use($temp_tgl){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl.'-1 day')));
                                $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                            })
                            ->where('tipe', 2)
                            ->where('id_shift', $shift)
                            ->where('status_produk', 2)
                            ->sum('jumlah');
                    }

                    $temp[$i] = (double)$res;
                    $i++;
                }
            }
            
            array_push($data, $temp);

            $temp_tgl = date('Y-m-d', strtotime($temp_tgl.'+1 day'));
        } while ($temp_tgl != $tgl_akhir);
        

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = [$data, $gudang];

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getTonaseAlatBerat()
    {
        $tanggal    = request()->input('tanggal');
        $shift      = request()->input('shift');
        $pilih_gudang     = request()->input('gudang');

        $date = explode('-', $tanggal);

        $tgl_awal   = date('Y-m-d', strtotime($date[0]));
        $tgl_akhir  = date('Y-m-d', strtotime($date[1].'+1 day'));

        $temp_tgl = $tgl_awal;

        if ($pilih_gudang) {
            $gudang = Gudang::where('id', $pilih_gudang)->get();
        } else {
            $gudang = Gudang::internal()->get();
        }

        $data = [];
        do {
            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $i = 1;
            if ($pilih_gudang) {
                if ($shift == 3) {
                    $angkut = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                        $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0);
                    })
                    ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                    ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                    ->whereNotNull('status_produk')
                    ->where(function($query) use ($pilih_gudang){
                        $query->where('aktivitas_harian.id_gudang', $pilih_gudang);
                        $query->orWhere('material_adjustment.id_gudang', $pilih_gudang);
                    })
                    ->where(function($query) use($temp_tgl){
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl.'-1 day')));
                        $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                    })
                    ->whereNotNull('butuh_alat_berat')
                    ->where('id_shift', $shift)
                    ->sum('material_trans.jumlah')
                    ;
                } else {
                    $angkut = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                        $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->where('draft', 0);
                    })
                    ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                    ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                    ->whereNotNull('status_produk')
                    ->where(function($query) use ($pilih_gudang){
                        $query->where('aktivitas_harian.id_gudang', $pilih_gudang);
                        $query->orWhere('material_adjustment.id_gudang', $pilih_gudang);
                    })
                    ->where(function($query) use($temp_tgl){
                        $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl);
                        $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                    })
                    ->whereNotNull('butuh_alat_berat')
                    ->where('id_shift', $shift)
                    ->sum('material_trans.jumlah')
                    ;
                }

                $temp[$i] = (double)$angkut;
            } else {
                foreach ($gudang as $key) {
                    if ($shift == 3) {
                        $angkut = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                            ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                        ->whereNotNull('status_produk')
                        ->where(function($query) use ($key){
                            $query->where('aktivitas_harian.id_gudang', $key->id);
                            $query->orWhere('material_adjustment.id_gudang', $key->id);
                        })
                        ->where(function($query) use($temp_tgl){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date('Y-m-d', strtotime($temp_tgl.'-1 day')));
                            $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                        })
                        ->whereNotNull('butuh_alat_berat')
                        ->where('id_shift', $shift)
                        ->sum('material_trans.jumlah')
                        ;
                    } else {
                        $angkut = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                            ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                        ->whereNotNull('status_produk')
                        ->where(function($query) use ($key){
                            $query->where('aktivitas_harian.id_gudang', $key->id);
                            $query->orWhere('material_adjustment.id_gudang', $key->id);
                        })
                        ->where(function($query) use($temp_tgl){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl);
                            $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                        })
                        ->whereNotNull('butuh_alat_berat')
                        ->where('id_shift', $shift)
                        ->sum('material_trans.jumlah')
                        ;
                    }
    
                    $temp[$i] = (double)$angkut;
                    $i++;
                }
            }

            array_push($data, $temp);

            $temp_tgl = date('Y-m-d', strtotime($temp_tgl.'+1 day'));
        } while ($temp_tgl != $tgl_akhir);

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = [$data, $gudang];

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function map()
    {
        $data['title'] = 'Map Click';
        return view('dashboard.mapclick', $data);
    }
    public function handlingPerJenisProduk(){
        $jenisProduk = DB::table(
                        DB::raw('(select id_material from (
                                SELECT trans.id_material, date(akt.created_at) as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where (id_shift = 1 or id_shift = 2) and mat.kategori = 1
                                union all
                                SELECT trans.id_material, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where id_shift = 3 and mat.kategori = 1
                            ) a group by id_material) a'
                       ))
                       ->select('id_material', 'nama')
                       ->orderBy('id_material','asc')
                       ->join('material as mat','mat.id','=','a.id_material')
                       ->get();
        $handling = DB::table(
                        DB::raw('(
                                SELECT trans.id_material, date(akt.created_at) as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where (id_shift = 1 or id_shift = 2) and mat.kategori = 1
                                union all
                                SELECT trans.id_material, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where id_shift = 3 and mat.kategori = 1
                            ) a'
                        )
                    )->selectRaw('tgl_akt, id_material, sum(jumlah) as jumlah')
                    ->groupBy(['tgl_akt', 'id_material'])
                    ->orderBy('tgl_akt','asc')
                    ->orderBy('id_material','asc')
                    ->get();
        $jp = [];
        $i=1;
        $cData = [];
        $cData[0] = "Periode";
        foreach($jenisProduk as $row){
            $jp[$i] = $row->id_material;
            $cData[$i] = $row->nama;
            $i++;
        }
        $tanggal = "";
        $i = 0;
        $no = 0;
        $rData = [];
        foreach($handling as $row){
            if($tanggal !== $row->tgl_akt){
                if($no > 0)
                    $i++;
                
                
                $rData[$i][0] = date('d-m-Y',strtotime($row->tgl_akt));

                $idx = 1;
                foreach($jp as $val){
                    $rData[$i][$idx] = 0;
                    $idx++;
                }

                $id_mat = array_search($row->id_material,$jp);
                $rData[$i][$id_mat] = (float)$row->jumlah;

                $tanggal = $row->tgl_akt;
            } else {
                $id_mat = array_search($row->id_material,$jp);

                $rData[$i][$id_mat] = (float)$row->jumlah;
            }
            $no++;
        }
        $data["cData"] = $cData;
        $data["rData"] = array_values($rData);
        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
    public function handlingPerJenisGudang(){
        $jenisProduk = DB::table(
                        DB::raw('(select id_gudang from (
                                SELECT trans.id_material, akt.id_gudang, date(akt.created_at) as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where (id_shift = 1 or id_shift = 2) and mat.kategori = 1
                                union all
                                SELECT trans.id_material, akt.id_gudang, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where id_shift = 3 and mat.kategori = 1
                            ) a group by id_gudang) a'
                       ))
                       ->select('id_gudang', 'nama')
                       ->orderBy('id_gudang','asc')
                       ->join('gudang as gdg','gdg.id','=','a.id_gudang')
                       ->get();
        $handling = DB::table(
                        DB::raw('(
                                SELECT trans.id_material, akt.id_gudang, date(akt.created_at) as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where (id_shift = 1 or id_shift = 2) and mat.kategori = 1
                                union all
                                SELECT trans.id_material, akt.id_gudang, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                where id_shift = 3 and mat.kategori = 1
                            ) a'
                        )
                    )->selectRaw('tgl_akt, id_gudang, sum(jumlah) as jumlah')
                    ->groupBy(['tgl_akt', 'id_gudang'])
                    ->orderBy('tgl_akt','asc')
                    ->orderBy('id_gudang','asc')
                    ->get();
        $jp = [];
        $i=1;
        $cData = [];
        $cData[0] = "Periode";
        foreach($jenisProduk as $row){
            $jp[$i] = $row->id_gudang;
            $cData[$i] = $row->nama;
            $i++;
        }
        $tanggal = "";
        $i = 0;
        $no = 0;
        $rData = [];
        foreach($handling as $row){
            if($tanggal !== $row->tgl_akt){
                if($no > 0)
                    $i++;
                
                
                $rData[$i][0] = date('d-m-Y',strtotime($row->tgl_akt));

                $idx = 1;
                foreach($jp as $val){
                    $rData[$i][$idx] = 0;
                    $idx++;
                }

                $id_mat = array_search($row->id_gudang,$jp);
                $rData[$i][$id_mat] = (float)$row->jumlah;

                $tanggal = $row->tgl_akt;
            } else {
                $id_mat = array_search($row->id_gudang,$jp);

                $rData[$i][$id_mat] = (float)$row->jumlah;
            }
            $no++;
        }
        $data["cData"] = $cData;
        $data["rData"] = array_values($rData);
        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}