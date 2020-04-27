<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\AlatBeratKerusakan;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\MaterialTrans;
use App\Http\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $AKTIVITAS_UPDATED_AT_FULLDATE = "TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')";
    private $FORMAT_FULLDATE = 'Y-m-d H:i:s';
    private $FORMAT_DATE = 'Y-m-d';
    private $START_SHIFT3 = ' 23:00:00 -1 day';
    private $START_SHIFT1 = ' 07:00:00';
    private $START_SHIFT2 = ' 15:00:00';
    private $INCREMENT_DAY = "+1 day";
    private $DECREMENT_DAY = "-1 day";

    public function index()
    {
        $data['title'] = 'Dashboard';

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
                
                if ($temp) {
                    array_push(${'shift'.$i}, $temp);
                }
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

                if ($temp) {
                    array_push(${'komplain_gp_shift' . $i}, $temp);
                }
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
        $tanggal    = request()->input('tanggal')??date('d-m-Y').'/'.date('d-m-Y');
        $shift      = request()->input('shift')??[1,2,3];
        $pilih_gudang     = request()->input('gudang');
        $data = [];

        if ($pilih_gudang == null) {
            $gudang = Gudang::select('id')->internal()->orderBy('id')->get()->pluck('id')->toArray();
        } else {
            $gudang = $pilih_gudang;
        }

        $date = explode('/', $tanggal);

        $queryShift = '';
        if ( $shift != null ) {
            $queryShift = ' and (id_shift = '.$shift[0].' ';
            for ($i=0; $i<count($shift); $i++) {
                $queryShift .= ' or id_shift = '.$shift[$i].' ';
            }
            $queryShift .= ') ';
        }

        $queryGudang = '';
        if ( $gudang != null ) {
            $queryGudang = ' and (id_gudang = '.$gudang[0].' ';
            for ($i=0; $i<count($gudang); $i++) {
                $queryGudang .= ' or id_gudang = '.$gudang[$i].' ';
            }

            $queryGudang .= ') ';
        }
        
        $queryTanggal = '';
        if ( $tanggal != null ) {
            $queryTanggal = " and TO_CHAR(jam_rusak, 'yyyy-mm-dd') BETWEEN '".date($this->FORMAT_DATE, strtotime($date[0]))."' and '".date($this->FORMAT_DATE, strtotime($date[1]))."' ";
        }

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
            $queryTanggal) as jumlah")
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
        $tanggal    = request()->input('tanggal')??date('d-m-Y').'/'.date('d-m-Y');
        $shift      = request()->input('shift')??[1,2,3];
        $pilih_gudang     = request()->input('gudang');

        if ($pilih_gudang == null) {
            $gudang = Gudang::select('id')->internal()->orderBy('id')->get()->pluck('id')->toArray();
        } else {
            $gudang = $pilih_gudang;
        }

        $date = explode('/', $tanggal);

        $tgl_awal = date($this->FORMAT_DATE, strtotime($date[0]));
        $tgl_akhir = date($this->FORMAT_DATE, strtotime($date[1].$this->INCREMENT_DAY));

        $gudangStok = GudangStok::distinct()
        ->select('id_gudang')
        ->with('gudang')
        ->where(function($query) use($gudang) {
            for ($i=0; $i < count($gudang); $i++) {
                $query->orWhere('id_gudang', $gudang[$i]);
            }
        })
        ->whereHas('material', function ($query) {
            $query->where('kategori', 2);
        })
        ->get();

        $tempJumlahPallet[0] = 0;
        $tempJumlahPallet[1] = 0;
        $tempJumlahPallet[2] = 0;

        $data = [];

        foreach ($gudangStok as $gudang) {
            for ($i=0; $i<3; $i++) {
                $saldoAwal = 0;
                $tempJumlahPallet[$i] = 0;
                for ($index_shift=0; $index_shift < count($shift); $index_shift++) {
                    if ($shift[$index_shift] == 1) {
                        $pre_masuk     = MaterialTrans::
                        leftJoin('aktivitas_harian', function($join) use($tgl_awal, $gudang){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                                ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                                ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_awal)));
                        })
                        ->leftJoin('gudang_stok', function ($join){
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                        })
                        ->where(function ($query) use ($tgl_awal) {
                            $query->where(function($query) use($tgl_awal){
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT1)));
                                $query->orWhere(function($query) use($tgl_awal){
                                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT1)));
                                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT2)));
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
                        ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                        ->where('tipe', 2)
                        ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                        ->sum('material_trans.jumlah');
    
                        $pre_keluar     = MaterialTrans::
                        leftJoin('aktivitas_harian', function($join) use($tgl_awal, $gudang){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                                ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                                ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_awal)));
                        })
                        ->leftJoin('gudang_stok', function ($join){
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                        })
                        ->where(function ($query) use ($tgl_awal) {
                            $query->where(function($query) use($tgl_awal){
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT1)));
                                $query->orWhere(function($query) use($tgl_awal){
                                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT1)));
                                    $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT2)));
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
                    } else if ($shift[$index_shift] == 2) {
                        $pre_masuk     = MaterialTrans::
                        leftJoin('aktivitas_harian', function($join) use($tgl_awal, $gudang){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                                ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                                ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_awal)));
                        })
                        ->leftJoin('gudang_stok', function ($join){
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                        })
                        ->where(function ($query) use ($tgl_awal) {
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT2)));
                            $query->orWhere(function($query) use($tgl_awal){
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT2)));
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . ' 23:00:00')));
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
                        ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                        ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                        ->sum('material_trans.jumlah');
    
                        $pre_keluar     = MaterialTrans::
                        leftJoin('aktivitas_harian', function($join) use($tgl_awal, $gudang){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                                ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                                ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_awal)));
                        })
                        ->leftJoin('gudang_stok', function ($join){
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                        })
                        ->where(function ($query) use ($tgl_awal) {
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT2)));
                            $query->orWhere(function($query) use($tgl_awal){
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT2)));
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . ' 23:00:00')));
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
                        ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                        ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                        ->sum('material_trans.jumlah');
    
                        $saldoAwal = $pre_masuk - $pre_keluar;
                    } else if ($shift[$index_shift] == 3) {
                        $pre_masuk = MaterialTrans::
                        leftJoin('aktivitas_harian', function($join) use($tgl_awal, $gudang){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                                ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                                ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_awal)));
                        })
                        ->leftJoin('gudang_stok', function ($join){
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                        })
                        ->where(function ($query) use ($tgl_awal) {
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                            $query->orWhere(function($query) use($tgl_awal){
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . ' 00:30:00')));
                                $query->where('id_shift', 2);
                            });
                            $query->orWhere('material_adjustment.tanggal', '<=', date($this->FORMAT_DATE, strtotime($tgl_awal . $this->DECREMENT_DAY)));
                            $query->orWhere(function ($query) use ($tgl_awal) {
                                    $query->where('material_adjustment.tanggal', '=', date($this->FORMAT_DATE, strtotime($tgl_awal . $this->DECREMENT_DAY)));
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
                        ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                        ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                        ->sum('material_trans.jumlah');
    
                        $pre_keluar = MaterialTrans::
                        leftJoin('aktivitas_harian', function($join) use($tgl_awal, $gudang){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                                ->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                        })
                        ->leftJoin('material_adjustment', function ($join) use ($tgl_awal, $gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                                ->where('material_adjustment.tanggal', '<', date($this->FORMAT_DATE, strtotime($tgl_awal)));
                        })
                        ->leftJoin('gudang_stok', function ($join){
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok');
                        })
                        ->where(function ($query) use ($tgl_awal) {
                            $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                            $query->orWhere(function($query) use($tgl_awal){
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '>=', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)));
                                $query->where(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), '<', date($this->FORMAT_FULLDATE, strtotime($tgl_awal . ' 00:30:00')));
                                $query->where('id_shift', 2);
                            });
                            $query->orWhere('material_adjustment.tanggal', '<=', date($this->FORMAT_DATE, strtotime($tgl_awal . $this->DECREMENT_DAY)));
                            $query->orWhere(function ($query) use ($tgl_awal) {
                                    $query->where('material_adjustment.tanggal', '=', date($this->FORMAT_DATE, strtotime($tgl_awal . $this->DECREMENT_DAY)));
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
                        ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                        ->where('status_pallet', ($i+2)) //harus + 2 step agar cocok dengan status pada databse
                        ->sum('material_trans.jumlah');
    
                        $saldoAwal = $pre_masuk - $pre_keluar;
                    }

                    $peralihanTambah = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($gudang){
                        $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                            ->where('draft', 0)
                            ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                            ;
                        })
                        ->leftJoin('material_adjustment', function($join) use($gudang){
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                            ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                            ;
                        })
                        ->leftJoin('gudang_stok', function($join) use($gudang) {
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
                            ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                            ;
                        })
                        ->where(function($query) use($tgl_awal, $tgl_akhir, $shift, $index_shift){
                            $query->whereBetween(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), [date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)), date($this->FORMAT_FULLDATE, strtotime($tgl_akhir . $this->START_SHIFT3))]);
                            $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                            $query->orWhere(function ($query) use ($tgl_awal, $tgl_akhir, $shift, $index_shift) {
                                $query->whereBetween('material_trans.tanggal', [$tgl_awal, $tgl_akhir]);
                                $query->whereNull('material_trans.id_aktivitas_harian');
                                $query->whereNull('material_trans.id_adjustment');
                                $query->where('material_trans.shift_id', $shift[$index_shift]);
                            });
                        })
                        ->where('tipe', 2)
                        ->where('status_pallet', ($i + 2))
                        ->where(function($query) use($shift, $index_shift) {
                            $query->where('id_shift', $shift[$index_shift]);
                            $query->orWhere('shift', $shift[$index_shift]);
                        })
                        ->sum('material_trans.jumlah');

                    $peralihanKurang = MaterialTrans::leftJoin('aktivitas_harian', function($join) use($gudang){
                        $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                            ->where('draft', 0)
                            ->where('aktivitas_harian.id_gudang', $gudang->id_gudang)
                            ;
                        })
                        ->leftJoin('material_adjustment', function($join) use($gudang) {
                            $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                            ->where('material_adjustment.id_gudang', $gudang->id_gudang)
                            ;
                        })
                        ->leftJoin('gudang_stok', function($join) use($gudang) {
                            $join->on('gudang_stok.id', '=', 'material_trans.id_gudang_stok')
                            ->where('gudang_stok.id_gudang', $gudang->id_gudang)
                            ;
                        })
                        ->where(function($query) use($tgl_awal, $tgl_akhir, $shift, $index_shift){
                            $query->whereBetween(DB::raw($this->AKTIVITAS_UPDATED_AT_FULLDATE), [date($this->FORMAT_FULLDATE, strtotime($tgl_awal . $this->START_SHIFT3)), date($this->FORMAT_FULLDATE, strtotime($tgl_akhir . $this->START_SHIFT3))]);
                            $query->orWhereBetween('material_adjustment.tanggal', [$tgl_awal, $tgl_akhir]);
                            $query->orWhere(function ($query) use ($tgl_awal, $tgl_akhir, $shift, $index_shift) {
                                $query->whereBetween('material_trans.tanggal', [$tgl_awal, $tgl_akhir]);
                                $query->whereNull('material_trans.id_aktivitas_harian');
                                $query->whereNull('material_trans.id_adjustment');
                                $query->where('material_trans.shift_id', $shift[$index_shift]);
                            });
                        })
                        ->where('tipe', 1)
                        ->where('status_pallet', ($i + 2))
                        ->where(function($query) use($shift, $index_shift) {
                            $query->where('id_shift', $shift[$index_shift]);
                            $query->orWhere('shift', $shift[$index_shift]);
                        })
                        ->sum('material_trans.jumlah');

                    $tempJumlahPallet[$i] += $saldoAwal+$peralihanTambah-$peralihanKurang;
                }

            }

            $temp = [
                $gudang->gudang->nama, 
                $tempJumlahPallet[0],
                $tempJumlahPallet[1],
                $tempJumlahPallet[2],
                ($tempJumlahPallet[0]+$tempJumlahPallet[1]+$tempJumlahPallet[2]),
            ];

            array_push($data, $temp);
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = [$data, date('d/m/Y', strtotime($tgl_awal)), date('d/m/Y', strtotime($tgl_akhir.$this->DECREMENT_DAY))];

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getProduksiPengeluaran()
    {
        $tanggal    = request()->input('tanggal')??date('d-m-Y').'/'.date('d-m-Y');
        $shift      = request()->input('shift')??[1,2,3];
        $pilih_gudang     = request()->input('gudang');

        if ($pilih_gudang == null) {
            $gudang = Gudang::select('id')->internal()->orderBy('nama')->get()->pluck('id')->toArray();
        } else {
            $gudang = $pilih_gudang;
        }

        $date = explode('/', $tanggal);
        
        $tgl_awal = date($this->FORMAT_DATE, strtotime($date[0]));
        $tgl_akhir = date($this->FORMAT_DATE, strtotime($date[1].$this->INCREMENT_DAY));

        $data = [];
        $temp_tgl = $tgl_awal;
        do {
            $transaksiKeluar = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian');
            })
            ->leftJoin('aktivitas', function($join) {
                $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                ;
            })
            ->whereNotNull('status_produk')
            ->where('draft', 0)
            ->where('tipe', 1)
            ->where(function($query) use($gudang){
                for ($i=0; $i < count($gudang); $i++) {
                    $query->orWhere('aktivitas_harian.id_gudang', $gudang[$i]);
                }
            })
            ->where(function($query) use($shift){
                for ($i=0; $i < count($shift); $i++) {
                    $query->orWhere('aktivitas_harian.id_shift', $shift[$i]);
                }
            })
            ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl)
            ->where('aktivitas.status_aktivitas', 1)
            ->where('status_produk', 1)
            ->sum('material_trans.jumlah')
            ;
    
            $transaksiMasuk = MaterialTrans::leftJoin('aktivitas_harian', function($join){
                $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                ;
            })
            ->leftJoin('aktivitas', function($join) {
                $join->on('aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                ;
            })
            ->whereNotNull('status_produk')
            ->where('draft', 0)
            ->where('tipe', 2)
            ->where(function($query) use($gudang){
                for ($i=0; $i < count($gudang); $i++) {
                    $query->orWhere('aktivitas_harian.id_gudang', $gudang[$i]);
                }
            })
            ->where(function($query) use($shift){
                for ($i=0; $i < count($shift); $i++) {
                    $query->orWhere('aktivitas_harian.id_shift', $shift[$i]);
                }
            })
            ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl)
            ->where('aktivitas.status_aktivitas', 2)
            ->where('status_produk', 1)
            ->sum('material_trans.jumlah')
            ;

            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $temp[1] = (double)$transaksiMasuk;
            $temp[2] = (double)$transaksiKeluar;

            array_push($data, $temp);

            $temp_tgl = date($this->FORMAT_DATE, strtotime($temp_tgl.$this->INCREMENT_DAY));
        } while ($temp_tgl != $tgl_akhir);
        
        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getPemuatanProduk()
    {
        $tanggal    = request()->input('tanggal')??date('d-m-Y').'/'.date('d-m-Y');
        $shift      = request()->input('shift')??[1,2,3];
        $pilih_gudang     = request()->input('gudang');

        if ($pilih_gudang == null) {
            $gudang = Gudang::select('id')->internal()->orderBy('nama')->get()->pluck('id')->toArray();
        } else {
            $gudang = $pilih_gudang;
        }

        $date = explode('/', $tanggal);

        $tgl_awal = date($this->FORMAT_DATE, strtotime($date[0]));
        $tgl_akhir = date($this->FORMAT_DATE, strtotime($date[1].$this->INCREMENT_DAY));

        $temp_tgl = $tgl_awal;

        $data = [];
        do {
            $realisasiMuat = 0;
            $kapasitasMuatBuruh = 0;
            for ($i=0; $i<count($gudang); $i++) {
                for ($k=0; $k<count($shift); $k++) {
                    if ($shift[$k] == 3) {
                        $kapasitas = DB::table('realisasi')
                        ->leftJoin('rencana_harian', 'rencana_harian.id', '=', 'realisasi.id_rencana')
                        ->where('id_gudang', $gudang[$i])
                        ->where('id_shift', $shift[$k])
                        ->where('rencana_harian.tanggal', date($this->FORMAT_DATE, strtotime($temp_tgl.$this->DECREMENT_DAY)))
                        ->first()
                        ;
                        $kapasitasMuatBuruh = $kapasitasMuatBuruh+(($kapasitas->jumlah_buruh??0)*60);
        
                        $realisasiMuat += MaterialTrans::leftJoin('aktivitas_harian', function($join){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                            ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                        ->whereNotNull('status_produk')
                        ->where(function($query) use ($gudang, $i){
                            $query->orWhere('aktivitas_harian.id_gudang', $gudang[$i]);
                            $query->orWhere('material_adjustment.id_gudang', $gudang[$i]);  
                        })
                        ->where(function($query) use($temp_tgl){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date($this->FORMAT_DATE, strtotime($temp_tgl.$this->DECREMENT_DAY)));
                            $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                        })
                        ->whereNotNull('butuh_tkbm')
                        ->where('id_shift', $shift[$k])
                        ->sum('material_trans.jumlah')
                        ;
                    } else if ($shift[$k] == 2 || $shift[$k] == 1) {
                        $kapasitas = DB::table('realisasi')
                        ->leftJoin('rencana_harian', 'rencana_harian.id', '=', 'realisasi.id_rencana')
                        ->where('id_gudang', $gudang[$i])
                        ->where('id_shift', $shift[$k])
                        ->where('rencana_harian.tanggal', $temp_tgl)
                        ->first()
                        ;

                        $kapasitasMuatBuruh = $kapasitasMuatBuruh+($kapasitas->jumlah_buruh??0)*60;
        
                        $realisasiMuat += MaterialTrans::leftJoin('aktivitas_harian', function($join){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                            ->where('draft', 0);
                        })
                        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        ->leftJoin('material_adjustment', 'material_adjustment.id', '=', 'id_adjustment')
                        ->whereNotNull('status_produk')
                        ->where(function($query) use ($gudang, $i){
                            $query->orWhere('aktivitas_harian.id_gudang', $gudang[$i]);
                            $query->orWhere('material_adjustment.id_gudang', $gudang[$i]);  
                        })
                        ->where(function($query) use($temp_tgl){
                            $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl);
                            $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                        })
                        ->whereNotNull('butuh_tkbm')
                        ->where('id_shift', $shift[$k])
                        ->sum('material_trans.jumlah')
                        ;
                    }
                }
            }

            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $temp[1] = $kapasitasMuatBuruh;
            $temp[2] = (double)$realisasiMuat;

            array_push($data, $temp);

            $temp_tgl = date($this->FORMAT_DATE, strtotime($temp_tgl.$this->INCREMENT_DAY));
        } while ($temp_tgl != $tgl_akhir);

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $data;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getTonaseProdukRusak()
    {
        $tanggal    = request()->input('tanggal')??date('d-m-Y').'/'.date('d-m-Y');
        $shift      = request()->input('shift')??[1,2,3];
        $pilih_gudang     = request()->input('gudang');

        if ($pilih_gudang == null) {
            $gudang = Gudang::select('id')->internal()->orderBy('id')->get()->pluck('id')->toArray();
            $daftarGudang = Gudang::internal()->orderBy('id')->get();
        } else {
            $gudang = $pilih_gudang;
            $daftarGudang = Gudang::internal()->whereIn('id', $pilih_gudang)->get();
        }

        $date = explode('/', $tanggal);
        $tgl_awal = date($this->FORMAT_DATE, strtotime($date[0]));
        $tgl_akhir = date($this->FORMAT_DATE, strtotime($date[1].$this->INCREMENT_DAY));

        $temp_tgl = $tgl_awal;
        $data = [];
        do {
            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $j = 1;
            for ($i=0; $i<count($gudang); $i++) {
                $totalProduk = 0;
                for ($k=0; $k<count($shift); $k++) {
                    if ($shift[$k] == 3) {
                        $totalProduk += MaterialTrans::leftJoin('aktivitas_harian', function($join) use($gudang, $i){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang[$i])
                                ;
                            })
                            ->leftJoin('material_adjustment', function($join) use($gudang, $i) {
                                $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang[$i])
                                ;
                            })
                            ->where(function($query) use($temp_tgl){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date($this->FORMAT_DATE, strtotime($temp_tgl.$this->DECREMENT_DAY)));
                                $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                            })
                            ->where('tipe', 2)
                            ->where('id_shift', $shift[$k])
                            ->where('status_produk', 2)
                            ->sum('jumlah');
                    } else if ($shift[$k] == 2 || $shift[$k] == 1) {
                        $totalProduk += MaterialTrans::leftJoin('aktivitas_harian', function($join) use($gudang, $i){
                            $join->on('aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                                ->where('draft', 0)
                                ->where('aktivitas_harian.id_gudang', $gudang[$i])
                                ;
                            })
                            ->leftJoin('material_adjustment', function($join) use($gudang, $i) {
                                $join->on('material_adjustment.id', '=', 'material_trans.id_adjustment')
                                ->where('material_adjustment.id_gudang', $gudang[$i])
                                ;
                            })
                            ->where(function($query) use($temp_tgl){
                                $query->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), date($this->FORMAT_DATE, strtotime($temp_tgl)));
                                $query->orWhere('material_adjustment.tanggal', $temp_tgl);
                            })
                            ->where('tipe', 2)
                            ->where('id_shift', $shift[$k])
                            ->where('status_produk', 2)
                            ->sum('jumlah');
                    }
                }

                $temp[$j] = (double)$totalProduk;
                $j++;
            }
            
            array_push($data, $temp);

            $temp_tgl = date($this->FORMAT_DATE, strtotime($temp_tgl.$this->INCREMENT_DAY));
        } while ($temp_tgl != $tgl_akhir);
        

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = [$data, $daftarGudang];

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getTonaseAlatBerat()
    {
        $tanggal    = request()->input('tanggal')??date('d-m-Y').'/'.date('d-m-Y');
        $shift      = request()->input('shift')??[1,2,3];
        $pilih_gudang     = request()->input('gudang');

        if ($pilih_gudang == null) {
            $gudang = Gudang::select('id')->internal()->orderBy('id')->get()->pluck('id')->toArray();
            $daftarGudang = Gudang::internal()->orderBy('id')->get();
        } else {
            $gudang = $pilih_gudang;
            $daftarGudang = Gudang::internal()->whereIn('id', $pilih_gudang)->get();
        }

        $date = explode('/', $tanggal);

        $tgl_awal   = date($this->FORMAT_DATE, strtotime($date[0]));
        $tgl_akhir  = date($this->FORMAT_DATE, strtotime($date[1].$this->INCREMENT_DAY));

        $temp_tgl = $tgl_awal;

        $data = [];
        do {
            $temp[0] = date('d-m-Y', strtotime($temp_tgl));
            $j = 1;
            
            for ($i=0; $i<count($gudang); $i++) {
                $totalAngkut = 0;
                for ($k=0; $k<count($shift); $k++) {
                    if ($shift[$k] == 3) {
                        $totalAngkut += MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        ->whereNotNull('status_produk')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $gudang[$i])
                        ->whereBetween(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd HH24-MI-SS')"), [
                            date($this->FORMAT_FULLDATE, strtotime($temp_tgl.' 23:00:00 '.$this->DECREMENT_DAY)), 
                            date($this->FORMAT_FULLDATE, strtotime($temp_tgl.' 23:00:00 '))
                        ])
                        ->whereNotNull('butuh_alat_berat')
                        ->where('id_shift', $shift[$k])
                        ->sum('material_trans.jumlah')
                        ;
                    } else if ($shift[$k] == 2 || $shift[$k] == 1) {
                        $totalAngkut += MaterialTrans::leftJoin('aktivitas_harian', 'aktivitas_harian.id', '=', 'material_trans.id_aktivitas_harian')
                        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
                        ->whereNotNull('status_produk')
                        ->where('draft', 0)
                        ->where('aktivitas_harian.id_gudang', $gudang[$i])
                        ->where(DB::raw("TO_CHAR(aktivitas_harian.updated_at, 'yyyy-mm-dd')"), $temp_tgl)
                        ->whereNotNull('butuh_alat_berat')
                        ->where('id_shift', $shift[$k])
                        ->sum('material_trans.jumlah')
                        ;
                    }
                }
                
                $temp[$j] = (double)$totalAngkut;
                $j++;
            }

            array_push($data, $temp);

            $temp_tgl = date($this->FORMAT_DATE, strtotime($temp_tgl.$this->INCREMENT_DAY));
        } while ($temp_tgl != $tgl_akhir);

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = [$data, $daftarGudang];

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function map()
    {
        $data['title'] = 'Map Click';
        return view('dashboard.mapclick', $data);
    }

    public function handlingPerJenisProduk(Request $req){
        $shift = $req->get("shift")??[1,2,3];
        $gudang = $req->get("gudang");
        $tanggal = $req->get("tanggal")??date('d-m-Y').'/'.date('d-m-Y');
        $tgl = explode("/", $tanggal);
        $tanggal_awal = date($this->FORMAT_DATE,strtotime($tgl[0]));
        $tanggal_akhir = date($this->FORMAT_DATE,strtotime($tgl[1]));
        $where1_2 = "where date(akt.created_at) BETWEEN '{$tanggal_awal}' AND '{$tanggal_akhir}'";
        $where3 = "where date(akt.created_at) BETWEEN '{$tanggal_awal}' AND '{$tanggal_akhir}'";

        if($shift != ""){
            $where_shift1_2 = "";
            for($i=0;$i < count($shift); $i++){
                if($shift[$i] == 1 or $shift[$i] == 2){
                    if($i == 0)
                        $where_shift1_2 .= "id_shift = {$shift[$i]}";
                    else
                        $where_shift1_2 .= " or id_shift = {$shift[$i]}";
                }
            }
            if($where_shift1_2 != ""){
                $where1_2 .= " and ({$where_shift1_2})";
            }
            $where_shift3 = "";
            for($i=0;$i < count($shift); $i++){
                if($shift[$i] == 3){
                    $where_shift3 .= "id_shift = {$shift[$i]}";
                }
            }
            if($where_shift3 != ""){
                $where3 .= " and ({$where_shift3})";
            }
        } else {
            $where1_2 .= " and (id_shift = 1 or id_shift = 2) and mat.kategori = 1";
            $where3 .= " and id_shift = 3 and mat.kategori = 1";
        }
        if($gudang != ""){
            $where1_2 .= " and (";
            for($i=0;$i < count($gudang); $i++){
                if($i == 0)
                    $where1_2 .= "id_gudang = {$gudang[$i]}";
                else
                    $where1_2 .= " or id_gudang = {$gudang[$i]}";
            }
            $where1_2 .= ")";

            $where3 .= " and (";
            for($i=0;$i < count($gudang); $i++){
                if($i == 0)
                    $where3 .= "id_gudang = {$gudang[$i]}";
                else
                    $where3 .= "or id_gudang = {$gudang[$i]}";
            }
            $where3 .= ")";
        }

        if($where_shift1_2 != "" and $where_shift3 != ""){
            $v_handling_per_jenis_produk = "SELECT trans.id_material, date(akt.created_at) as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                {$where1_2}
                                union all
                                SELECT trans.id_material, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                {$where3}
                                ";
        } else {
            $v_handling_per_jenis_produk = "SELECT trans.id_material, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                {$where3}";
        }
        $jenisProduk = DB::table(
                        DB::raw("(select id_material from (
                                {$v_handling_per_jenis_produk}
                            ) a group by id_material) a"
                       ))
                       ->select('id_material', 'nama')
                       ->orderBy('id_material','asc')
                       ->join('material as mat','mat.id','=','a.id_material')
                       ->get();
        $handling = DB::table(
                        DB::raw("(
                                {$v_handling_per_jenis_produk}
                            ) a"
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
    public function handlingPerJenisGudang(Request $req){
        $shift = $req->get("shift")??[1,2,3];
        $gudang = $req->get("gudang");
        $tanggal = $req->get("tanggal")??date('d-m-Y').'/'.date('d-m-Y');
        $tgl = explode("/", $tanggal);
        $tanggal_awal = date($this->FORMAT_DATE,strtotime($tgl[0]));
        $tanggal_akhir = date($this->FORMAT_DATE,strtotime($tgl[1]));
        $where1_2 = "where date(akt.created_at) BETWEEN '{$tanggal_awal}' AND '{$tanggal_akhir}'";
        $where3 = "where date(akt.created_at) BETWEEN '{$tanggal_awal}' AND '{$tanggal_akhir}'";

        if($shift != ""){
            $where_shift1_2 = "";
            for($i=0;$i < count($shift); $i++){
                if($shift[$i] == 1 or $shift[$i] == 2){
                    if($i == 0)
                        $where_shift1_2 .= "id_shift = {$shift[$i]}";
                    else
                        $where_shift1_2 .= " or id_shift = {$shift[$i]}";
                }
            }
            if($where_shift1_2 != ""){
                $where1_2 .= " and ({$where_shift1_2})";
            }
            $where_shift3 = "";
            for($i=0;$i < count($shift); $i++){
                if($shift[$i] == 3){
                    $where_shift3 .= "id_shift = {$shift[$i]}";
                }
            }
            if($where_shift3 != ""){
                $where3 .= " and ({$where_shift3})";
            }
        } else {
            $where1_2 .= " and (id_shift = 1 or id_shift = 2) and mat.kategori = 1";
            $where3 .= " and id_shift = 3 and mat.kategori = 1";
        }
        if($gudang != ""){
            $where1_2 .= " and (";
            for($i=0;$i < count($gudang); $i++){
                if($i == 0)
                    $where1_2 .= "id_gudang = {$gudang[$i]}";
                else
                    $where1_2 .= " or id_gudang = {$gudang[$i]}";
            }
            $where1_2 .= ")";

            $where3 .= " and (";
            for($i=0;$i < count($gudang); $i++){
                if($i == 0)
                    $where3 .= "id_gudang = {$gudang[$i]}";
                else
                    $where3 .= "or id_gudang = {$gudang[$i]}";
            }
            $where3 .= ")";
        }

        if($where_shift1_2 != "" and $where_shift3 != ""){
            $v_handling_per_gudang = "SELECT trans.id_material, akt.id_gudang, date(akt.created_at) as tgl_akt, 
                                akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                {$where1_2}
                                union all
                                SELECT trans.id_material, akt.id_gudang, date(akt.created_at) - 1 as tgl_akt, akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                {$where3}
                                ";
        } else {
            $v_handling_per_gudang = "SELECT trans.id_material, akt.id_gudang, date(akt.created_at) - 1 as tgl_akt, 
                                akt.id_shift, trans.jumlah, akt.id_shift
                                FROM public.material_trans trans
                                join aktivitas_harian akt on akt.id = trans.id_aktivitas_harian 
                                join material mat on mat.id = trans.id_material
                                {$where3}";
        }
        $jenisProduk = DB::table(
                        DB::raw("(select id_gudang from (
                                {$v_handling_per_gudang}
                            ) a group by id_gudang) a"
                       ))
                       ->select('id_gudang', 'nama')
                       ->orderBy('id_gudang','asc')
                       ->join('gudang as gdg','gdg.id','=','a.id_gudang')
                       ->get();
        $handling = DB::table(
                        DB::raw("(
                                {$v_handling_per_gudang}
                            ) a"
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