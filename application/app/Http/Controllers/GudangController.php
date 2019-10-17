<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\Karu;
use App\Http\Models\StokMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GudangController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index()
    {
        $data['title'] = 'Gudang';
        // $data['menu_active'] = 'master';
        // $data['sub_menu_active'] = 'jenis foto';
        $data['material'] = Material::where('kategori', 3)->get();
        $data['karu'] = Karu::all();
        return view('gudang.grid', $data);
    }

    public function json(Request $req)
    {
        $models = new Gudang();

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

    public function store(Request $req, Gudang $models)
    {
        $rules = [
            'nama'              => 'required',
            'id_sloc'           => 'numeric',
            'id_plant'          => 'numeric',
            'tipe_gudang'       => 'required|numeric|digits_between:1,2',
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $action = $req->input('action');
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $this->responseCode                 = 400;
            $this->responseStatus               = 'Missing Param';
            $this->responseMessage              = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log']    = $validator->errors();
        } else {
            $id = $req->input('id');
            // $nama = $req->input('nama');
            // $id_sloc = $req->input('id_sloc');
            // $id_plant = $req->input('id_plant');
            // $tipe_gudang = $req->input('tipe_gudang');

            $start_date  = null;
            if ($req->input('start_date') != '') {
                $start_date  = date('Y-m-d', strtotime($req->input('start_date')));
            }

            $end_date   = null;
            if ($req->input('end_date') != '') {
                $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
            }

            if (!empty($id)) {
                $models = Gudang::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $models->nama           = strip_tags($req->input('nama'));
            $models->id_karu        = strip_tags($req->input('id_karu'));
            $models->id_sloc        = strip_tags($req->input('id_sloc'));
            $models->id_plant       = strip_tags($req->input('id_plant'));
            $models->tipe_gudang    = strip_tags($req->input('tipe_gudang'));
            $models->start_date     = $start_date;
            $models->end_date       = $end_date;

            $saved = $models->save();
            if (!$saved) {
                $this->responseCode     = 502;
                $this->responseMessage  = 'Data gagal disimpan!';

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            } else {
                $material = $req->input('material');
                $stok_min = $req->input('stok_min');
                for ($i = 0; $i < count($material); $i++) {
                    $resource = StokMaterial::where('id_gudang', $models->id)->where('id_material', $material[$i])->first();
                    
                    if (!empty($resource)) {
                        StokMaterial::where('id_gudang', $models->id)
                            ->where('id_material', $material[$i])
                            ->update(['stok_min' => $stok_min[$i]]);
                        // $resource->stok_min = $stok_min[$i];
                        // $resource->save();
                        // print_r($resource->stok_min);
                    } else {
                        $stok_material = new StokMaterial();

                        $stok_material->id_gudang = $models->id;
                        $stok_material->id_material = $material[$i];
                        $stok_material->stok_min = $stok_min[$i];
                        $stok_material->save();
                        // echo 'assegfdh';
                    }
                }

                // $stok_material = new StokMaterial();
                // print_r($stok_material);

                $this->responseCode = 200;
                $this->responseMessage = 'Data berhasil disimpan';
            }
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function loadMaterial($id_gudang)
    {
        $models = StokMaterial::where('id_gudang', $id_gudang)->get();
        $this->responseCode = 200;
        $this->responseData = $models;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, Gudang $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::find($id);

            if (!empty($res)) {
                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia.';
                $this->responseData = $res;
            } else {
                $this->responseData = [];
                $this->responseStatus = 'No Data Available';
                $this->responseMessage = 'Data tidak tersedia';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function edit(Gudang $gudang)
    {
        //
    }

    public function update(Request $request, Gudang $gudang)
    {
        //
    }

    public function destroy(Gudang $gudang)
    {
        //
    }
}
