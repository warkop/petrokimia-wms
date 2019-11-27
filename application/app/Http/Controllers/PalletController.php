<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\GudangPallet;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use Illuminate\Http\Request;

class PalletController extends Controller
{
    public function index($id_gudang)
    {
        $gudang = Gudang::find($id_gudang);
        $data['nama_gudang'] = $gudang->nama;
        $data['id_gudang'] = $id_gudang;
        return view('list-pallet.grid', $data);
    }

    public function json(Request $req, $id_gudang)
    {
        $models = new GudangPallet();

        $numbcol = $req->get('order');
        $columns = $req->get('columns');

        $echo    = $req->get('draw');
        $start   = $req->get('start');
        $perpage = $req->get('length');

        $search  = $req->get('search');
        $search  = $search['value'];
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search  = preg_replace($pattern, '', $search);

        $sort = $numbcol[0]['dir'];
        $field = $columns[$numbcol[0]['column']]['data'];

        $condition = '';

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $result = $models->gridJson($start, $perpage, $search, false, $sort, $field, $condition, $id_gudang);
            $total  = $models->gridJson($start, $perpage, $search, true, $sort, $field, $condition, $id_gudang);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function getMaterial()
    {
        $data = Material::pallet();

        return response()->json($data, 200);
    }

    public function store(Request $req)
    {
        # code...
    }

    public function destroy(GudangPallet $gudangPallet)
    {
        $gudangPallet->forceDelete();
    }
}
