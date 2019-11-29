<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AreaStok;
use App\Http\Models\MaterialTrans;
use Illuminate\Http\Request;

class PenerimaanGpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('penerimaan-gp.grid');
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

        $condition = [
            'id_gudang' => $gudang ?? '',
            'id_shift'  => $shift ?? '',
        ];

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AktivitasHarian $aktivitasHarian)
    {
        $data['aktivitasHarian'] = $aktivitasHarian;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();
        $res = AreaStok::select(
            'id_area',
            'nama',
            'jumlah'
        )
        ->leftJoin('area', 'area.id', '=', 'area_stok.id_area');

        // MaterialTrans::select(
        //     'jumlah'
        // )   
        // ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
        // ->where('')
        // ->get(); 
        $data['produk'] = MaterialTrans::where('id_aktivitas_harian', $aktivitasHarian->id)->where('status_produk', 1)->get();
        return view('penerimaan-gp.detail', $data);
    }

    public function getArea(Request $req, AreaStok $areaStok)
    {

        return response()->json($areaStok, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
