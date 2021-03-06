<?php

namespace App\Http\Controllers;

use App\Http\Models\Material;
use App\Http\Models\Sap;
use App\Http\Requests\MaterialRequest;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Material';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'material';
        return view('master.master-material.grid', $data);
    }

    public function json(Request $req)
    {
        $models = new Material();

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

    public function store(MaterialRequest $req, Material $material)
    {
        $req->validate();

        $id = $req->input('id');
        if (!empty($id)) {
            $material = Material::withoutGlobalScopes()->find($id);
        }

        $material->id_material_sap    = $req->input('id_material_sap');
        $material->nama               = $req->input('nama');
        $material->kategori           = $req->input('kategori');
        $material->berat              = $req->input('berat');
        $material->koefisien_pallet   = $req->input('koefisien_pallet');
        $material->start_date         = $req->input('start_date');
        $material->end_date           = $req->input('end_date');

        $material->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getMaterialSap($id_material_sap) {
        $sap = Sap::where('MaterialNumber', $id_material_sap)->first();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $sap;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, Material $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::withoutGlobalScopes()->find($id);

            $sap = Sap::where('MaterialNumber', $res->id_material_sap)->first();

            if (!empty($res)) {
                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia.';
                $this->responseData = $res;
                $this->responseData['sap'] = $sap;
            } else {
                $this->responseData = [];
                $this->responseStatus = 'No Data Available';
                $this->responseMessage = 'Data tidak tersedia';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function getSap(Request $request, Sap $sap)
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $search = $request->input('term');
            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $res = Sap::select(
                'MaterialNumber'
            )
            ->where('MaterialNumber', 'LIKE', "%".$search."%")
            ->get();

            if (!empty($res)) {
                $responseCode = 200;
                $responseMessage = 'Data tersedia.';
                $responseData = $res;
            } else {
                $responseData = [];
                $responseStatus = 'No Data Available';
                $responseMessage = 'Data Jalan tidak tersedia';
            }

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
            return response()->json($response, $responseCode);
        }
    }
}
