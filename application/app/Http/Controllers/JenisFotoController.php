<?php

namespace App\Http\Controllers;

use App\Http\Models\JenisFoto;
use App\Http\Requests\JenisFotoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JenisFotoController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Jenis Foto';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'jenis foto';
        return view('master/master-jenis-foto/grid', $data);
    }

    public function create()
    {
        //
    }

    public function json(Request $req)
    {
        
        $models = new JenisFoto();

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

    public function store(JenisFotoRequest $req, JenisFoto $jenisFoto)
    {
        $req->validated();

        $jenisFoto->nama           = $req->input('nama');
        $jenisFoto->start_date     = $req->input('start_date');
        $jenisFoto->end_date       = $req->input('end_date');

        $jenisFoto->save();

        $this->responseCode = Response::HTTP_OK;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id, JenisFoto $models, Request $request)
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

    public function edit(JenisFoto $jenisFoto)
    {
        //
    }

    public function update(Request $request, JenisFoto $jenisFoto)
    {
        //
    }

    public function destroy(JenisFoto $jenisFoto)
    {
        //
    }
}
