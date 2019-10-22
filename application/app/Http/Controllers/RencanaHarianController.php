<?php

namespace App\Http\Controllers;

use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\Realisasi;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Users;
use App\Http\Requests\RealisasiRequest;
use App\Http\Requests\RencanaHarianRequest;
use Illuminate\Http\Request;

class RencanaHarianController extends Controller
{
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
        $data['checker']        = TenagaKerjaNonOrganik::checker()->get();
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
        $id = $req->input('id');
        if (!empty($id)) {
            $rencana_harian = RencanaHarian::find($req->input('id'));

            RencanaAlatBerat::where('id_rencana', $rencana_harian->id)->forceDelete();
            RencanaTkbm::where('id_rencana', $rencana_harian->id)->forceDelete();
            RencanaAreaTkbm::where('id_rencana', $rencana_harian->id)->forceDelete();
        } else {
            $rencana_harian = new RencanaHarian();
        }

        //rencana harian
        $rencana_harian->tanggal                = date('Y-m-d');
        $rencana_harian->id_shift               = $req->input('id_shift');
        $rencana_harian->start_date             = date('Y-m-d');
        $rencana_harian->created_at             = date('Y-m-d H:i:s');
        $rencana_harian->save();

        //rencana alat berat
        $alat_berat = $req->input('alat_berat');
        for ($i=0; $i<count($alat_berat); $i++) {
            $arr = [
                'id_rencana' => $rencana_harian->id,
                'id_alat_berat' => $alat_berat[$i],
            ];
            \DB::table('rencana_alat_berat')->insert(
                $arr
            );
        }

        //rencana tkbm
        $admin_loket = $req->input('admin_loket');
        foreach ($admin_loket as $key => $value) {
            $arr = [
                'id_rencana' => $rencana_harian->id,
                'id_tkbm' => $value
            ];

            \DB::table('rencana_tkbm')->insert(
                $arr
            );
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
        }

        //rencana area tkbm
        $housekeeper = $req->input('housekeeper');
        if (!empty($housekeeper)) {
            foreach ($housekeeper as $key => $value) {
                if (!empty($req->input('area')[$key])) {
                $area = $req->input('area')[$key];
                    foreach ($area as $row => $hey) {
                        $arr = [
                            'id_rencana' => $rencana_harian->id,
                            'id_tkbm' => $value,
                            'id_area' => $hey,
                        ];
        
                        \DB::table('rencana_area_tkbm')->insert(
                            $arr
                        );
                    }
                }
            }
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show(RencanaHarian $rencana_harian)
    {
        $rencana_harian;
        $this->responseCode = 200;
        $this->responseData = $rencana_harian;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\RencanaHarian  $rencanaHarian
     * @return \Illuminate\Http\Response
     */
    public function edit(RencanaHarian $rencana_harian)
    {
        $alat_berat = new AlatBerat;
        $data['id']             = $rencana_harian->id;
        $data['tanggal']        = date('d/m/Y', strtotime($rencana_harian->tanggal));
        $data['alat_berat']     = $alat_berat->getWithRelation();
        $data['checker']        = TenagaKerjaNonOrganik::checker()->get();
        $data['op_alat_berat']  = TenagaKerjaNonOrganik::operatorAlatBerat()->get();
        $data['admin_loket']    = TenagaKerjaNonOrganik::adminLoket()->get();
        $data['shift_kerja']    = ShiftKerja::all();
        $data['tkbm_rencana']    = RencanaAreaTkbm::select('id_rencana','id_tkbm')->where('id_rencana', $rencana_harian->id)->groupBy('id_tkbm', 'id_rencana')->get();
        // $data['area_rencana']    = RencanaAreaTkbm::select('id_rencana','id_area')->where('id_rencana', $rencana_harian->id)->groupBy('id_tkbm', 'id_rencana')->get();
        return view('rencana-harian.add', $data);
    }

    public function getRencanaTkbm($id_job_desk, $id_rencana)
    {
        $resource = new RencanaTkbm();

        $res = $resource->join('tenaga_kerja_non_organik as tkbm', 'tkbm.id', '=', 'id_tkbm')
        ->where('id_rencana', $id_rencana)
        ->where('job_desk_id', $id_job_desk)
        ->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getRencanaAreaTkbm($id_rencana, $id_tkbm='')
    {
        $resource = new RencanaAreaTkbm();

        $res = $resource
        ->where('id_rencana', $id_rencana)
        ->get();

        if ($id_tkbm != '') {
            $res->where('id_tkbm', $id_tkbm);
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getRencanaAlatBerat($id_rencana)
    {
        $resource = new RencanaAlatBerat();

        $res = $resource->where('id_rencana', $id_rencana)
        ->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getTkbm($id_job_desk)
    {
        $res = TenagaKerjaNonOrganik::where('job_desk_id', $id_job_desk)->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getAlatBerat()
    {
        $alat_berat = new AlatBerat;
        $res = $alat_berat->getWithRelation();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getArea()
    {
        $users = Users::find(\Auth::id());
        $gudang = Gudang::where('id_karu', $users->id_karu)->first();
        $res = Area::where('id_gudang', $gudang->id)->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function realisasi(RencanaHarian $rencanaHarian)
    {
        RencanaAreaTkbm::where('id_rencana', $rencanaHarian->id)->get();
        $data['id_rencana_harian'] = $rencanaHarian->id;
        return view('rencana-harian.realisasi', $data);
    }

    public function storeRealisasi(RealisasiRequest $req, Realisasi $realisasi)
    {
        $req->validated();

        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function destroy(RencanaHarian $rencanaHarian)
    {
        //
    }
}
