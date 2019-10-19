<?php

namespace App\Http\Controllers;

use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\JobDesk;
use App\Scopes\EndDateScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenagaKerjaNonOrganikController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index()
    {
        $data['title'] = 'Master Tenaga Kerja Non Organik';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'tenaga kerja non organik';
        $data['job_desk'] = JobDesk::all();
        return view('master.master-tenaga-kerja-nonorganik.grid', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function json(Request $req)
    {
        $models = new TenagaKerjaNonOrganik();

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

    public function store(Request $req, TenagaKerjaNonOrganik $models)
    {
        $id = $req->input('id');
        $rules = [
            'nama'              => ['required'],
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
            $job_desk_id = $req->input('job_desk_id');
            $nama = $req->input('nama');
            $nomor_hp = $req->input('nomor_hp');
            $nomor_bpjs = $req->input('nomor_bpjs');

            $start_date  = null;
            if ($req->input('start_date') != '') {
                $start_date  = date('Y-m-d', strtotime($req->input('start_date')));
            }

            $end_date   = null;
            if ($req->input('end_date') != '') {
                $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
            }

            if (!empty($id)) {
                $models = TenagaKerjaNonOrganik::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $models->nama                   = strip_tags($nama);
            $models->job_desk_id            = $job_desk_id;
            $models->nomor_hp               = $nomor_hp;
            $models->nomor_bpjs             = $nomor_bpjs;
            $models->start_date             = $start_date;
            $models->end_date               = $end_date;

            $models->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, TenagaKerjaNonOrganik $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::withoutGlobalScope(EndDateScope::class)->find($id);

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
     * @param  \App\TenagaKerjaNonOrganik  $tenagaKerjaNonOrganik
     * @return \Illuminate\Http\Response
     */
    public function edit(TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TenagaKerjaNonOrganik  $tenagaKerjaNonOrganik
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TenagaKerjaNonOrganik  $tenagaKerjaNonOrganik
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        //
    }
}
