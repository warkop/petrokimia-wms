<?php

namespace App\Http\Controllers;

use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\AlatBerat;
use App\Http\Requests\AlatBeratRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AlatBeratController extends Controller
{
    public function index($id_kategori)
    {
        $kategori = KategoriAlatBerat::withoutGlobalScopes()->find($id_kategori);
        $data['id_kategori'] = $id_kategori;
        $data['nama_kategori'] = $kategori->nama;
        return view('list-alat-berat.grid', $data);
    }

    public function json(Request $req, $id_kategori)
    {
        $models = new AlatBerat();

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
            $result = $models->jsonGrid($start, $perpage, $search, false, $sort, $field, $condition, $id_kategori);
            $total  = $models->jsonGrid($start, $perpage, $search, true, $sort, $field, $condition, $id_kategori);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function store(AlatBeratRequest $req, AlatBerat $models, $id_kategori)
    {
        $req->validated();
        $id = $req->input('id');

        if (!empty($id)) {
            $models = AlatBerat::find($id);
        }

        $models->nomor_lambung = strtoupper($req->input('nomor_lambung'));
        $models->id_kategori = $id_kategori;

        $models->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

  
    public function show(KategoriAlatBerat $kategoriAlatBerat, AlatBerat $alatBerat)
    {
        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $alatBerat;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function edit(KategoriAlatBerat $kategoriAlatBerat)
    {
        //
    }

    public function update(Request $request, KategoriAlatBerat $kategoriAlatBerat)
    {
        //
    }

    public function destroy(KategoriAlatBerat $models)
    {
        KategoriAlatBerat::destroy($models->shift_kerja_id);
        $res = KategoriAlatBerat::find($models->shift_kerja_id);
        if (!empty($res)) {
            $this->responseCode = 500;
            $this->responseMessage = 'Data gagal dihapus';
            $this->responseData = [];
        } else {
            $this->responseData = [];
            $this->responseStatus = 'No Data Available';
            $this->responseMessage = 'Data berhasil dihapus';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
