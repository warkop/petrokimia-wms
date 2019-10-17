<?php

namespace App\Http\Controllers;

use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\AlatBerat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AlatBeratController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index($id_kategori)
    {
        $kategori = KategoriAlatBerat::find($id_kategori);
        $data['id_kategori'] = $id_kategori;
        $data['nama_kategori'] = $kategori->nama;
        return view('list-alat-berat.grid', $data);
    }

    public function create()
    {
        //
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

    public function store(Request $req, AlatBerat $models, $id_kategori)
    {
        $id = $req->input('id');
        $rules = [
            'nomor_lambung'    => ['required', Rule::unique('alat_berat', 'nomor_lambung')->ignore($id)],
            'nomor_polisi'     => ['required', Rule::unique('alat_berat', 'nomor_polisi')->ignore($id)],
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
            if (!empty($id)) {
                $models = AlatBerat::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $models->nomor_lambung = strip_tags($req->input('nomor_lambung'));
            $models->nomor_polisi = strip_tags($req->input('nomor_polisi'));
            $models->id_kategori = $id_kategori;

            $models->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

  
    public function show($id_kategori, $id,AlatBerat $models, Request $request)
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

    public function edit(KategoriAlatBerat $kategoriAlatBerat)
    {
        //
    }

    public function update(Request $request, KategoriAlatBerat $kategoriAlatBerat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KategoriAlatBerat  $kategoriAlatBerat
     * @return \Illuminate\Http\Response
     */
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
