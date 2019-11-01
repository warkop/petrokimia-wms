<?php

namespace App\Http\Controllers;

use App\Http\Models\MaterialAdjusment;
use App\Http\Models\MaterialTrans;
use Illuminate\Http\Request;

class MaterialAdjusmentController extends Controller
{
    public function index($id)
    {
        $data['title'] = 'Stok Adjusment';

        $gudang = Gudang::find($id);
        if (!empty($gudang)) {
            return view('stok-adjusment.grid', $data);
        } else {
            abort(404);
        }

    }

    public function json(Request $req)
    {
        $models = new MaterialAdjusment();

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

    public function store(MaterialRequest $req, MaterialAdjusment $materialAdjusment)
    {
        $req->validate();

        $id = $req->input('id');
        if (!empty($id)) {
            $materialAdjusment = MaterialAdjusment::withoutGlobalScopes()->find($id);
        }

        //material adjusment
        $materialAdjusment->tanggal            = $req->input('tanggal');
        // $materialAdjusment->foto               = $req->input('foto');
        $materialAdjusment->save();

        //material trans
        $id_material = $req->input('id_material');

        foreach ($id_material as $key => $value) {
            $materialTrans = new MaterialTrans;
            $materialTrans->id_adjusment    = $materialAdjusment->id;
            $materialTrans->id_material     = $value;
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
