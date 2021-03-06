<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasKelayakanFoto;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\ShiftKerja;
use App\Http\Models\Sistro;
use App\Http\Resources\MaterialTransResource;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['shift'] = ShiftKerja::get();
        $data['gudang'] = Gudang::internal()->get();
        $data['produk'] = Material::produk()->get();
        return view('log-aktivitas.grid', $data);
    }

    public function json(Request $req)
    {
        $models = new AktivitasHarian();

        $numbcol = $req->get('order');
        $columns = $req->get('columns');

        $echo    = $req->get('draw');
        $start   = $req->get('start');
        $perpage = $req->get('length');

        $search  = $req->get('search');
        $search  = $search['value'];
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search  = preg_replace($pattern, '', $search);
        
        $gudang     = $req->get('gudang');
        $shift      = $req->get('shift');
        $start_date = $req->get('start_date');
        $end_date   = $req->get('end_date');
        $produk   = $req->get('produk');

        $sort = $numbcol[0]['dir'];
        $field = $columns[$numbcol[0]['column']]['data'];
        $condition = [];
        if ($gudang != '') {
            $condition['id_gudang'] = $gudang;
        }

        if ($shift != '') {
            $condition['id_shift'] = $shift;
        }

        if ($start_date) {
            $condition['start_date'] = $start_date;
        }

        if ($end_date) {
            $condition['end_date'] = $end_date;
        }

        if ($produk) {
            $condition['produk'] = $produk;
        }

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $temp = $models->jsonGrid($start, $perpage, $search, $sort, $field, $condition);
            $result = $temp['result'];
            $total  = $temp['count'];
        } else {
            $temp = $models::orderBy($field, $sort)->get();
            $result = $temp;
            $total  = $temp->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AktivitasHarian $aktivitasHarian)
    {
        $data['title'] = 'Detail Aktivitas';
        $data['aktivitasHarian'] = $aktivitasHarian->load('checker.checker', 'karu.karu');
        $data['id_aktivitas_harian'] = $aktivitasHarian->id;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();
        $produk = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_produk')->get();
        $data['produk'] = MaterialTransResource::collection($produk);
        $pallet = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_pallet')->get();
        $data['pallet'] = $pallet;
        $data['id_gudang'] = $aktivitasHarian->id_gudang;
        $data['list_produk'] = Material::produk()->get();
        $data['fotoKelayakanBefore'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 1)->get();
        $data['fotoKelayakanAfter'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 2)->get();
        return view('log-aktivitas.detail', $data);
    }

    public function getArea($id_gudang, $id_material, $id_aktivitas_harian)
    {
        $areaStok = MaterialTrans::with('areaStok.area')
        ->where('id_aktivitas_harian', $id_aktivitas_harian)
        ->where('id_material', $id_material)
        ->get();
        return response()->json($areaStok, 200);
    }

    public function print($id)
    {
        $aktivitasHarian = AktivitasHarian::with('checker.checker', 'karu.karu')->where('id', $id)
            ->whereHas('aktivitas', function ($query) {
                $query->whereNotNull('pengiriman');
                $query->orWhereNotNull('so');
                $query->withoutGlobalScopes();
            })->firstOrFail();
        $data['title'] = 'Cetak Aktivitas';
        $data['aktivitasHarian'] = $aktivitasHarian;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();

        $produk = MaterialTrans::select(
            'material_trans.id_material',
            'material.nama as nama_material',
            'area.nama as nama_area',
            'area_stok.id_area',
            'material_trans.tipe',
            'area_stok.tanggal',
            'material_trans.jumlah'
        )
            ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
            ->leftJoin('area_stok', 'area_stok.id', '=', 'material_trans.id_area_stok')
            ->leftJoin('area', 'area_stok.id_area', '=', 'area.id')
            ->where('id_aktivitas_harian', $aktivitasHarian->id)
            ->whereNotNull('status_produk')
            ->get();
        $data['produk'] = $produk;
        $pallet = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_pallet')->get();
        $data['pallet'] = $pallet;
        $data['fotoKelayakanBefore'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 1)->get();
        $data['fotoKelayakanAfter'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 2)->get();

        if($aktivitasHarian->canceled == NULL)
        return view('log-aktivitas.cetak', $data);
        else
        return view('log-aktivitas.cetak-cancel', $data); 
    }
}
