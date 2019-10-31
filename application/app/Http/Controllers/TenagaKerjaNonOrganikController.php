<?php

namespace App\Http\Controllers;

use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\JobDesk;
use App\Http\Requests\TenagaKerjaNonOrganikRequest;
use App\Scopes\EndDateScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenagaKerjaNonOrganikController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Tenaga Kerja Non Organik';
        $data['job_desk'] = JobDesk::all();
        return view('master.master-tenaga-kerja-nonorganik.grid', $data);
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

    public function store(TenagaKerjaNonOrganikRequest $req, TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        $req->validated();

        $id = $req->input('id');

        if (!empty($id)) {
            $tenagaKerjaNonOrganik = TenagaKerjaNonOrganik::withoutGlobalScopes()->find($id);
        } 

        $tenagaKerjaNonOrganik->nama                   = $req->input('nama');
        $tenagaKerjaNonOrganik->nik                    = $req->input('nik');
        $tenagaKerjaNonOrganik->job_desk_id            = $req->input('job_desk_id');
        $tenagaKerjaNonOrganik->nomor_hp               = $req->input('nomor_hp');
        $tenagaKerjaNonOrganik->nomor_bpjs             = $req->input('nomor_bpjs');
        $tenagaKerjaNonOrganik->end_date               = $req->input('end_date');

        $tenagaKerjaNonOrganik->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

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

    public function edit(TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        //
    }

    public function update(Request $request, TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        //
    }

    public function destroy(TenagaKerjaNonOrganik $tenagaKerjaNonOrganik)
    {
        //
    }
}
