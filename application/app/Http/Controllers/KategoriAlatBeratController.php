<?php

namespace App\Http\Controllers;

use App\Http\Models\KategoriAlatBerat;
use App\Http\Requests\KategoriAlatBeratRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KategoriAlatBeratController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Kategori Alat Berat';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'kategori alat berat';
        return view('master.master-alat-berat.grid', $data);
    }

    public function create()
    {
        //
    }

    public function json(Request $req)
    {
        $models = new KategoriAlatBerat();

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

    public function store(KategoriAlatBeratRequest $req, KategoriAlatBerat $models)
    {
        $req->validated();
        $id = $req->input('id');
        
        // $tampung_anggaran = ($req->input('anggaran') ? $req->input('anggaran') : 0);
        // $tampung_anggaran = str_replace('.', '', $tampung_anggaran);
        // $tampung_anggaran = str_replace(',', '.', $tampung_anggaran);
        // $anggaran = $tampung_anggaran;

        $end_date   = null;
        if ($req->input('end_date') != '') {
            $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
        }

        if (!empty($id)) {
            $models = KategoriAlatBerat::withoutGlobalScopes()->find($id);
        }

        $models->nama           = $req->input('nama');
        // $models->anggaran       = $anggaran;
        $models->end_date       = $end_date;

        $models->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, KategoriAlatBerat $models, Request $request)
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KategoriAlatBerat  $kategoriAlatBerat
     * @return \Illuminate\Http\Response
     */
    public function edit(KategoriAlatBerat $kategoriAlatBerat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KategoriAlatBerat  $kategoriAlatBerat
     * @return \Illuminate\Http\Response
     */
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
