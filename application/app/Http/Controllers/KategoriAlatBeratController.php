<?php

namespace App\Http\Controllers;

use App\Http\Models\KategoriAlatBerat;
use App\Http\Requests\KategoriAlatBeratRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KategoriAlatBeratController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];
    
    public function index()
    {
        return view('master.master-alat-berat.grid');
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

    public function store(Request $req, KategoriAlatBerat $models)
    {
        $id = $req->input('id');
        $rules = [
            'nama'    => ['required', Rule::unique('alat_berat_kat', 'nama')->ignore($id, 'id')],
            'start_date'                  => 'nullable|date_format:d-m-Y',
            'end_date'                    => 'nullable|date_format:d-m-Y|after:start_date',
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
            $temp_model = KategoriAlatBerat::whereNotNull('forklift')->first();
            if (empty($temp_model) || ($temp_model->id == $id && !empty($temp_model))) {
                $tampung_anggaran = ($req->input('anggaran') ? $req->input('anggaran') : 0);
                $tampung_anggaran = str_replace('.', '', $tampung_anggaran);
                $tampung_anggaran = str_replace(',', '.', $tampung_anggaran);
                $anggaran = $tampung_anggaran;
    
                $forklift = $req->input('forklift');
    
                $start_date  = null;
                if ($req->input('start_date') != '') {
                    $start_date  = date('Y-m-d', strtotime($req->input('start_date')));
                }
    
                $end_date   = null;
                if ($req->input('end_date') != '') {
                    $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
                }
    
                if (!empty($id)) {
                    $models = KategoriAlatBerat::find($id);
                    $models->updated_by = session('userdata')['id_user'];
                } else {
                    $models->created_by = session('userdata')['id_user'];
                }
    
                $models->nama           = strip_tags($req->input('nama'));
                $models->anggaran       = $anggaran;
                $models->forklift       = $forklift;
                $models->start_date     = $start_date;
                $models->end_date       = $end_date;
    
                $models->save();
    
                $this->responseCode = 200;
                $this->responseMessage = 'Data berhasil disimpan';
            } else {
                $this->responseCode                 = 400;
                $this->responseStatus               = 'Missing Param';
                $this->responseMessage              = 'Kategori forklift sudah ada pada data lain!';
            }

        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, KategoriAlatBerat $models, Request $request)
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
