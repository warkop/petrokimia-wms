<?php

namespace App\Http\Controllers;

use App\Http\Models\AlatBerat;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Requests\RencanaHarianRequest;
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
        $data['title'] = 'Rencana Harian';
        return view('rencana-harian.grid', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Rencana Harian';
        $alat_berat = new AlatBerat;
        $data['alat_berat']     = $alat_berat->getWithRelation();
        $data['checker']        = TenagaKerjaNonOrganik::checker()->endDate()->get();
        $data['op_alat_berat']  = TenagaKerjaNonOrganik::operatorAlatBerat()->get();
        $data['admin_loket']    = TenagaKerjaNonOrganik::adminLoket()->get();
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

    public function store(RencanaHarianRequest $req)
    {
        $req->validated();
        
        if (!empty($id)) {
            $rencana_harian = RencanaHarian::find($req->input('id'));
            $rencana_harian->updated_by = session('userdata')['id_user'];
        } else {
            $rencana_harian = new RencanaHarian();
            $rencana_harian->created_by = session('userdata')['id_user'];
        }

        //rencana harian
        $rencana_harian->tanggal                = date('Y-m-d');
        $rencana_harian->id_shift               = $req->input('id_shift');
        $rencana_harian->start_date             = date('Y-m-d');
        $rencana_harian->created_at             = date('Y-m-d H:i:s');
        $rencana_harian->save();

        //rencana alat berat
        $rencana_alat_berat = new RencanaAlatBerat();
        $alat_berat = $req->input('alat_berat');
        // echo count($alat_berat);
        for ($i=0; $i<count($alat_berat); $i++) {
            $arr = [
                'id_rencana' => $rencana_harian->id,
                'id_alat_berat' => $alat_berat[$i],
            ];
            \DB::table('rencana_alat_berat')->insert(
                $arr
            );
            // $rencana_alat_berat->id_rencana = $rencana_harian->id;
            // $rencana_alat_berat->id_alat_berat = $alat_berat[$i];
            // $rencana_alat_berat->save();
            // $rencana_alat_berat->create($arr);
        }

        //rencana tkbm
        $rencana_tkbm = new RencanaTkbm();
        $admin_loket = $req->input('admin_loket');
        foreach ($admin_loket as $key => $value) {
            $arr = [
                'id_rencana' => $rencana_harian->id,
                'id_tkbm' => $value
            ];

            \DB::table('rencana_tkbm')->insert(
                $arr
            );

            // $rencana_tkbm->id_rencana = $rencana_harian->id;
            // $rencana_tkbm->id_tkbm = $value;
            // $rencana_tkbm->save();
        }
        
        $op_alat_berat = $req->input('op_alat_berat');
        foreach ($op_alat_berat as $key => $value) {
            $arr = [
                'id_rencana' => $rencana_harian->id,
                'id_tkbm' => $value
            ];

            \DB::table('rencana_tkbm')->insert(
                $arr
            );
            
            // $rencana_tkbm->id_rencana = $rencana_harian->id;
            // $rencana_tkbm->id_tkbm = $value;
            // $rencana_tkbm->save();
        }

        $checker = $req->input('checker');
        foreach ($checker as $key => $value) {
            $arr = [
                'id_rencana' => $rencana_harian->id,
                'id_tkbm' => $value
            ];

            \DB::table('rencana_tkbm')->insert(
                $arr
            );
            // $rencana_tkbm->id_rencana = $rencana_harian->id;
            // $rencana_tkbm->id_tkbm = $value;
            // $rencana_tkbm->save();
        }

        //rencana area tkbm
        $rencana_area_tkbm = new RencanaAreaTkbm();
        // $housekeeper = $req->input('housekeeper');
        // foreach ($housekeeper as $key => $value) {
        //     $rencana_area_tkbm->id_rencana = $rencana_harian->id;
        //     $rencana_area_tkbm->id_tkbm = $value;
        //     $rencana_area_tkbm->save();
        // }

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id)
    {
        $rencanaHarian = new RencanaHarian;
        $res = $rencanaHarian::find($id);

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\RencanaHarian  $rencanaHarian
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $alat_berat = new AlatBerat;
        $rencana_harian = RencanaHarian::find($id);
        $data['id']             = $rencana_harian->id;
        $data['tanggal']        = date('d/m/Y', strtotime($rencana_harian->tanggal));
        $data['alat_berat']     = $alat_berat->getWithRelation();
        $data['checker']        = TenagaKerjaNonOrganik::where('job_desk_id', 2)->get();
        $data['op_alat_berat']  = TenagaKerjaNonOrganik::where('job_desk_id', 3)->get();
        $data['admin_loket']    = TenagaKerjaNonOrganik::where('job_desk_id', 4)->get();
        $data['shift_kerja']    = ShiftKerja::whereNull('end_date')->get();
        return view('rencana-harian.add', $data);
    }

    public function getTkbm($id_rencana, $id_job_desk)
    {
        $res = RencanaHarian::where('id_rencana', $id_rencana);
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
