<?php

namespace App\Http\Controllers;

use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\Realisasi;
use App\Http\Models\RealisasiHousekeeper;
use App\Http\Models\RealisasiMaterial;
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
        $this->authorize('view', RencanaHarian::class);
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
            $rencana_harian = RencanaHarian::findOrFail($req->input('id'));

            RencanaAlatBerat::where('id_rencana', $rencana_harian->id)->forceDelete();
            RencanaTkbm::where('id_rencana', $rencana_harian->id)->forceDelete();
            RencanaAreaTkbm::where('id_rencana', $rencana_harian->id)->forceDelete();
        } else {
            $rencana_harian = new RencanaHarian();
        }

        $users = Users::findOrFail(\Auth::id());

        $res_gudang = Gudang::where('id_karu', $users->id_karu)->first();

        //rencana harian
        $rencana_harian->tanggal                = date('Y-m-d');
        $rencana_harian->id_shift               = $req->input('id_shift');
        $rencana_harian->start_date             = now();
        // $rencana_harian->created_at             = date('Y-m-d H:i:s');
        $rencana_harian->id_gudang              = $res_gudang->id;
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

    public function getArea($id_gudang ='')
    {
        $users = Users::find(\Auth::id());
        if ($id_gudang == '') {
            $gudang = Gudang::where('id_karu', $users->id_karu)->first();
            if (!empty($gudang)) {
                $res = Area::where('id_gudang', $gudang->id)->get();
        
                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia';
                $this->responseData = $res;
            } else {
                $this->responseCode = 403;
                $this->responseMessage = 'Anda tidak memiliki gudang! Silahkan daftarkan gudang Anda pada menu Gudang!';
            }
        } else {
            $res = Area::where('id_gudang', $id_gudang)->get();

            $this->responseCode = 200;
            $this->responseMessage = 'Data tersedia';
            $this->responseData = $res;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function realisasi(RencanaHarian $rencanaHarian)
    {
        $this->authorize('update', $rencanaHarian);
        $data['tkbm_rencana']    = RencanaAreaTkbm::select('id_rencana', 'id_tkbm')->where('id_rencana', $rencanaHarian->id)->groupBy('id_tkbm', 'id_rencana')->get();
        $data['material']    = Material::where('kategori', 3)->get();
        $data['id_rencana'] = $rencanaHarian->id;

        // $temp_realisasi = Realisasi::where('id_rencana', $rencanaHarian->id)->first();
        // $data['store_material'] = (new RealisasiMaterial)->where('id_realisasi', $temp_realisasi->id)->get();
        // $data['store_housekeeper'] = (new RealisasiHousekeeper)->select('id_tkbm')->where('id_realisasi', $temp_realisasi->id)->groupBy('id_tkbm')->get();
        // $data['store_area_housekeeper'] = (new RealisasiHousekeeper)->select('id_area', 'id_tkbm', 'nama')->leftJoin('area', 'area.id', '=', 'realisasi_housekeeper.id_area')->where('id_realisasi', $temp_realisasi->id)->get();
        return view('rencana-harian.realisasi', $data);
    }

    public function storeRealisasi(RealisasiRequest $req, $id_rencana, Realisasi $realisasi)
    {
        $req->validated();

        $temp_res = (new Realisasi)->where('id_rencana', $id_rencana)->first();
        (new Realisasi)->where('id_rencana', $id_rencana)->forceDelete();

        if (!empty($temp_res)) {
            (new RealisasiHousekeeper)->where('id_realisasi', $temp_res->id)->forceDelete();
            (new RealisasiMaterial)->where('id_realisasi', $temp_res->id)->forceDelete();
        }

        $realisasi->id_rencana = $id_rencana;
        $realisasi->tanggal = now();
        $realisasi->approve = $req->input('approve');
        $realisasi->save();

        $housekeeper = $req->input('housekeeper');
        $housekeeper = array_values($housekeeper);
        if (!empty($housekeeper)) {
            foreach ($housekeeper as $key => $value) {
                $temp = array_values($req->input('area_housekeeper')[$key]);
                if (!empty($temp)) {
                    foreach ($temp as $row => $hey) {
                        $arr = [
                            'id_realisasi' => $realisasi->id,
                            'id_tkbm' => $value,
                            'id_area' => $hey,
                        ];

                        \DB::table('realisasi_housekeeper')->insert(
                            $arr
                        );
                    }
                }
            }
        }

        
        // $material = $req->input('material');
        // $material_tambah = $req->input('material_tambah');
        // $material_kurang = $req->input('material_kurang');
        // $panjang = count($material);
        // $material           = array_values($material);
        // $material_tambah    = array_values($material_tambah);
        // $material_kurang    = array_values($material_kurang);
        // for($i=0; $i<$panjang; $i++) {
        //     $arr = [
        //         'id_realisasi' => $realisasi->id,
        //         'id_material' => $material[$i],
        //         'bertambah' => $material_tambah[$i],
        //         'berkurang' => $material_kurang[$i],
        //         'created_at' => now(),
        //     ];

        //     \DB::table('realisasi_material')->insert(
        //         $arr
        //     );
        // }

        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getMaterial($kategori)
    {
        $this->responseData = Material::where('kategori', $kategori)->get();
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);   
    }

    public function getHousekeeper($id_rencana)
    {
        // $id_rencana = $req->get('id_rencana');
        if (is_numeric($id_rencana)) {
            $this->responseData = RencanaAreaTkbm::select('id_tkbm', 'nama')
            ->where('id_rencana', $id_rencana)
            ->leftJoin('tenaga_kerja_non_organik', 'id_tkbm', '=', 'id')
            ->groupBy('id_tkbm', 'nama')
            ->orderBy('nama', 'asc')
            ->get();
            $this->responseCode = 200;
        } else {
            $this->responseMessage = 'ID rencana tidak ditemukan';
            $this->responseCode = 400;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);   
    }

    public function getGudang()
    {
        $user = \Auth::user();
        
        $gudang = Gudang::where('id_karu', $user->id_karu)->get();
        $this->responseData = $gudang;
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);   
    }

    public function destroy(RencanaHarian $rencanaHarian)
    {
        //
    }
}
