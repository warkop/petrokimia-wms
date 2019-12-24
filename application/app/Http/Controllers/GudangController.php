<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasGudang;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\Karu;
use App\Http\Models\StokMaterial;
use App\Http\Requests\GudangRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function getArea($id_gudang)
    {
        $data = Area::where('id_gudang', $id_gudang)->get();

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
        where('aktivitas.nama', 'LIKE', "%" . $search . "%")
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

    public function selectAktivitas(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id_aktivitas'  => 'required|exists:aktivitas,id',
            'id_gudang'     => 'required|exists:gudang,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $this->responseMessage = '';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json(['errors' => $errors, 'message' => 'Inputan tidak valid'], $this->responseCode);
        }

        $id_aktivitas   = $req->input('id_aktivitas');
        $id_gudang      = $req->input('id_gudang');
        $res = AktivitasGudang::where('id_gudang', $id_gudang)->where('id_aktivitas', $id_aktivitas)->get();
        if (!$res->isEmpty()) {
            $this->responseCode = 403;
            $this->responseMessage = 'Aktivitas sudah ada pada gudang ini';
        } else {
            $aktivitasGudang = new AktivitasGudang;
            $aktivitasGudang->id_aktivitas  = $id_aktivitas;
            $aktivitasGudang->id_gudang     = $id_gudang;
            $aktivitasGudang->save();
    
            $this->responseData = $aktivitasGudang;
            $this->responseCode = 200;
            $this->responseMessage = 'Aktivitas berhasil ditambahkan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function removeAktivitas($id_gudang, $id_aktivitas)
    {
        $res = AktivitasGudang::where('id_gudang', $id_gudang)->where('id_aktivitas', $id_aktivitas)->forceDelete();

        $res = AktivitasGudang::where('id_gudang', $id_gudang)->get();
        $this->responseData = $res;
        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

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

    public function layoutGudang($id_gudang)
    {
        $data['title'] = 'Layout Gudang';
        $data['id_gudang'] = $id_gudang;
        return view('gudang.layoutGudang', $data);
    }

    public function loadArea(Request $req, $id_gudang)
    {
        $search = $req->get('term');
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search = preg_replace($pattern, '', $search);
        
        $res = Area::where('id_gudang', $id_gudang)->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%'.strtolower($search).'%')->orderBy('nama')->get();

        $this->responseData = $res;
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function storeKoordinat(Request $req)
    {
        $validatedData = $req->validate([
            'koordinat' => 'required',
            'pilih_area' => 'required',
        ], [
            'required' => ':attribute wajib diisi!',
        ]);

        $koordinat      = $req->input('koordinat');
        $id_area        = $req->input('pilih_area');

        $area = Area::find($id_area);
        $area->koordinat = json_encode($koordinat);
        $area->save();

        $this->responseData     = $area;
        $this->responseMessage  = "Data berhasil disimpan";
        $this->responseCode     = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function loadKoordinat(Area $area)
    {
        $this->responseData     = json_decode($area->koordinat);
        $this->responseCode     = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
