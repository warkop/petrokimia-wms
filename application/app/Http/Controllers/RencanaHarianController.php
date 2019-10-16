<?php

namespace App\Http\Controllers;

use App\Http\Models\AlatBerat;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RencanaHarianController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index()
    {
        return view('rencana-harian.grid');
    }

    public function create()
    {
        $alat_berat = new AlatBerat;
        $data['alat_berat']     = $alat_berat->getWithRelation();
        $data['op_alat_berat']  = Users::where('role_id', 2)->get();
        $data['checker']        = Users::where('role_id', 3)->get();
        $data['admin_loket']    = Users::where('role_id', 4)->get();
        $data['shift_kerja']    = ShiftKerja::whereNull('end_date')->get(); 
        return view('rencana-harian.add', $data);
    }

    public function json(Request $req)
    {
        $models = new RencanaHarian();

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

    public function store(Request $req)
    {
        $id = $req->input('id');
        $id_shift = $req->input('id_shift');
        $rules = [
            'id_shift'          => [
                'required',
                Rule::exists('shift_kerja')->where(function ($query) use ($id_shift){
                    $query->where('id', $id_shift);
                }),
            ],
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
                $models = RencanaHarian::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $models->tanggal                = date('Y-m-d');
            $models->id_shift               = $job_desk_id;
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\RencanaHarian  $rencanaHarian
     * @return \Illuminate\Http\Response
     */
    public function show(RencanaHarian $rencanaHarian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\RencanaHarian  $rencanaHarian
     * @return \Illuminate\Http\Response
     */
    public function edit(RencanaHarian $rencanaHarian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\RencanaHarian  $rencanaHarian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RencanaHarian $rencanaHarian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\RencanaHarian  $rencanaHarian
     * @return \Illuminate\Http\Response
     */
    public function destroy(RencanaHarian $rencanaHarian)
    {
        //
    }
}
