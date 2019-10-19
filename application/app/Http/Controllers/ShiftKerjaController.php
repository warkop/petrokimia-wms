<?php

namespace App\Http\Controllers;

use App\Http\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShiftKerjaController extends Controller
{
    public function index()
    {
        return view('master.master-shift-kerja.grid');
    }

    public function json(Request $req)
    {
        $models = new ShiftKerja();

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

    public function create()
    {
        //
    }

    public function store(Request $req, ShiftKerja $models)
    {
        $id = $req->input('shift_kerja_id');
        $rules = [
            'nama_shift'        => ['required', Rule::unique('shift_kerja', 'nama_shift')->ignore($id, 'shift_kerja_id')],
            'mulai_shift'       => 'required|date_format:H:i',
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $action = $req->input('action');
        if ($action == 'edit') {
            $rules['shift_kerja_id'] = 'required';
        }

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $this->responseCode                 = 400;
            $this->responseStatus               = 'Missing Param';
            $this->responseMessage              = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log']    = $validator->errors();
        } else {
           

            $mulai_shift    = date('H:i', strtotime($req->input('mulai_shift')));
            $start_date  = null;
            if ($req->input('start_date') != '') {
                $start_date  = date('Y-m-d', strtotime($req->input('start_date')));
            }

            $end_date   = null;
            if ($req->input('end_date') != '') {
                $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
            }

            if (!empty($id)) {
                $models = ShiftKerja::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $models->nama_shift     = strip_tags($req->input('nama_shift'));
            $models->mulai_shift    = $mulai_shift;
            $models->start_date     = $start_date;
            $models->end_date       = $end_date;

            $models->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, ShiftKerja $models, Request $request)
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

    public function edit(ShiftKerja $shiftKerja)
    {
        //
    }

    public function update(Request $request, ShiftKerja $shiftKerja)
    {
        //
    }

    public function destroy(ShiftKerja $shiftKerja)
    {
        ShiftKerja::destroy($shiftKerja->shift_kerja_id);
        $res = ShiftKerja::find($shiftKerja->shift_kerja_id);
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
