<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\MaterialAdjustment;
use App\Http\Models\MaterialTrans;
use Illuminate\Http\Request;

class MaterialAdjustmentController extends Controller
{
    public function index($id)
    {
        $data['title'] = 'Stock Adjustment';

        $gudang = Gudang::find($id);
        if (!empty($gudang)) {
            return view('stock-adjustment.grid', $data);
        } else {
            abort(404);
        }
    }

    public function json(Request $req)
    {
        $models = new MaterialAdjustment();

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

    public function store(MaterialRequest $req, MaterialAdjustment $materialAdjustment)
    {
        $req->validate();

        $id = $req->input('id');
        if (!empty($id)) {
            $materialAdjustment = MaterialAdjustment::withoutGlobalScopes()->find($id);
        }

        //material adjusment
        $materialAdjustment->tanggal            = $req->input('tanggal');
        // $materialAdjustment->foto               = $req->input('foto');
        $materialAdjustment->save();

        //material trans
        $id_material = $req->input('id_material');

        foreach ($id_material as $key => $value) {
            $materialTrans = new MaterialTrans;
            $materialTrans->id_adjusment    = $materialAdjustment->id;
            $materialTrans->id_material     = $value;
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function uploadFile($id_material_adjustment)
    {
        
    }
}
