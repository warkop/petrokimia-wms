<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\ShiftKerja;
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
        $data['gudang'] = Gudang::get();
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
        $sort = $numbcol[0]['dir'];
        $field = $columns[$numbcol[0]['column']]['data'];
        $condition = [];
        if ($gudang != '') {
            $condition['id_gudang'] = $gudang;
        }

        if ($shift != '') {
            $condition['id_shift'] = $shift;
        }

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $result = $models->jsonGrid($start, $perpage, $search, false, $sort, $field, $condition);
            $total  = $models->jsonGrid($start, $perpage, $search, true, $sort, $field, $condition);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
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
        $data['aktivitasHarian'] = $aktivitasHarian;
        $data['id_aktivitas_harian'] = $aktivitasHarian->id;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();
        $res = AreaStok::select(
            'id_area',
            'nama',
            'jumlah'
        )
            ->leftJoin('area', 'area.id', '=', 'area_stok.id_area');

        $res = AreaStok::with('area')->get();

        $produk = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->where('status_produk', 1)->get();
        $data['produk'] = MaterialTransResource::collection($produk);
        $pallet = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_pallet')->get();
        $data['pallet'] = $pallet;
        $data['id_gudang'] = $aktivitasHarian->id_gudang;

        $data['list_produk'] = Material::produk()->get();
        return view('log-aktivitas.detail', $data);
    }

    public function getArea($id_gudang, $id_material, $id_aktivitas_harian)
    {
        $areaStok = Area::with('areaStok', 'areaStok.materialTrans')
            ->whereHas('areaStok', function ($query) use ($id_material) {
                $query->where('id_materials', $id_material);
            })
            ->whereHas('areaStok.materialTrans', function ($query) use ($id_aktivitas_harian) {
                $query->where('id_aktivitas_harian', $id_aktivitas_harian);
            })
            ->where('id_gudang', $id_gudang)
            ->orderBy('nama')
            ->get();
        return response()->json($areaStok, 200);
    }
}
