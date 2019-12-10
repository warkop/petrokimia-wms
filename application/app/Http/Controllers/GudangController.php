<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasGudang;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\Karu;
use App\Http\Models\StokMaterial;
use App\Http\Requests\GudangRequest;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $data['title'] = 'Gudang';
        $data['material'] = Material::where('kategori', 2)->get();
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

    public function store(GudangRequest $req, Gudang $models)
    {
        $req->validated();

        $id = $req->input('id');

        if (!empty($id)) {
            $models = Gudang::findOrFail($id);
        }

        if (!empty($req->input('id_karu'))) {
            $models->id_karu        = $req->input('id_karu');
        } else {
            $models->id_karu = null;
        }

        $models->nama           = $req->input('nama');
        $models->id_sloc        = $req->input('id_sloc');
        $models->id_plant       = $req->input('id_plant');
        $models->tipe_gudang    = $req->input('tipe_gudang');
        $models->start_date     = $req->input('start_date');
        $models->end_date       = $req->input('end_date');

        $saved = $models->save();
        if (!$saved) {
            $this->responseCode     = 502;
            $this->responseMessage  = 'Data gagal disimpan!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        } else {
            $material = $req->input('material');
            $stok_min = $req->input('stok_min');
            if (!empty($material)) {
                for ($i = 0; $i < count($material); $i++) {
                    $resource = StokMaterial::where('id_gudang', $models->id)->where('id_material', $material[$i])->first();
                    
                    if (!empty($resource)) {
                        StokMaterial::where('id_gudang', $models->id)
                            ->where('id_material', $material[$i])
                        ->update(['stok_min' => $stok_min[$i]]);
                        // $resource->stok_min = $stok_min[$i];
                        // $resource->save();
                    } else {
                        $stok_material = new StokMaterial();
    
                        $stok_material->id_gudang = $models->id;
                        $stok_material->id_material = $material[$i];
                        $stok_material->stok_min = $stok_min[$i];
                        $stok_material->save();
                    }
                }
            }
            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
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
            $res = $models::withoutGlobalScopes()->find($id);

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

    public function getProduk()
    {
        $data = Material::produk()->get();

        return response()->render(200, $data);
    }

    public function getPallet()
    {
        $data = Material::pallet()->get();

        return response()->render(200, $data);
    }

    public function getAktivitas(Request $request, $id_gudang)
    {
        $search = $request->input('term');
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search = preg_replace($pattern, '', $search);

        $res = Aktivitas::
        leftJoin('aktivitas_gudang', 'aktivitas.id', '=', 'aktivitas_gudang')
        ->whereNotIn('id_gudang', $id_gudang)
        ->where('aktivitas', 'LIKE', "%" . $search . "%")
        ->get();

        if (!empty($res)) {
            $this->responseCode = 200;
            $this->responseMessage = 'Data tersedia.';
            $this->responseData = $res;
        } else {
            $this->responseData = [];
            $this->responseStatus = 'No Data Available';
            $this->responseMessage = 'Data Jalan tidak tersedia';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getAktivitasGudang($id_gudang)
    {
        $res = AktivitasGudang::with('aktivitas')->where('id_gudang', $id_gudang)->get();
        $this->responseData = $res;
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
