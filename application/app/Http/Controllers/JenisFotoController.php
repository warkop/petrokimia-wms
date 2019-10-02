<?php

namespace App\Http\Controllers;

use App\Http\Models\JenisFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JenisFotoController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index()
    {
        return view('master/master-jenis-foto/grid');
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

    public function store(Request $req, JenisFoto $models)
    {
        $rules = [
            'nama_jenis_foto'   => 'required',
            'start_date'        => 'date_format:d-m-Y',
            'end_date'          => 'date_format:d-m-Y|after:from_date',
        ];

        $action = $req->input('action');
        if ($action == 'edit') {
            $rules['jenis_foto_id'] = 'required';
        }

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $this->responseCode                 = 400;
            $this->responseStatus               = 'Missing Param';
            $this->responseMessage              = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log']    = $validator->errors();
        } else {
            $jenis_foto_id = $req->input('jenis_foto_id');

            $from_date  = date('Y-m-d', strtotime($req->input('from_date')));
            $end_date   = date('Y-m-d', strtotime($req->input('end_date')));

            if (!empty($jenis_foto_id)) {
                $models = JenisFoto::find($jenis_foto_id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                // $models->created_by = session('userdata')['id_user'];
            }

            $models->nama_jenis_foto  = strip_tags($req->input('nama_jenis_foto'));
            $models->from_date  = $from_date;
            $models->end_date   = $end_date;

            $models->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($jenis_foto_id, JenisFoto $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models->get_data($jenis_foto_id);

            if (!empty($res)) {
                $responseCode = 200;
                $responseMessage = 'Data tersedia.';
                $responseData = $res;
            } else {
                $responseData = [];
                $responseStatus = 'No Data Available';
                $responseMessage = 'Data tidak tersedia';
            }

            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
            return response()->json($response, $responseCode);
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
