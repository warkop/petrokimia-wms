<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\Realisasi;
use App\Http\Models\RealisasiHousekeeper;
use App\Http\Models\RealisasiMaterial;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\RencanaHarian;
use App\Http\Models\Users;
use App\Http\Requests\ApiRealisasiRequest;
use App\Http\Resources\AktivitasResource;

class RealisasiController extends Controller
{
    public function index()
    {
        $data = Realisasi::get();

        return AktivitasResource::collection($data)->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);
    }

    public function show($id)
    {
        $realisasi = Realisasi::findOrFail($id);

        $realisasiHousekeeper = RealisasiHousekeeper::select(
            'realisasi_housekeeper.id',
            'realisasi_housekeeper.id_realisasi',
            'realisasi_housekeeper.id_tkbm',
            'realisasi_housekeeper.id_area',
            'tk.nama as nama_housekeeper',
            'area.nama as nama_area',
        )
        ->join('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'realisasi_housekeeper.id_tkbm')
        ->join('area', 'area.id', '=', 'realisasi_housekeeper.id_area')
        ->where('id_realisasi', $realisasi->id)->get();
        
        $realisasiMaterial = RealisasiMaterial::
        join('material', 'material.id', '=', 'realisasi_material.id_material')
        ->where('id_realisasi', $realisasi->id)
        ->get();

        $res = collect($realisasi);
        $res = $res->merge(['list_housekeeper' => $realisasiHousekeeper]);
        $res = $res->merge(['list_material' => $realisasiMaterial]);


        return (new AktivitasResource($res))->additional([
            'status' => [
                'message'   => '',
                'code'      => 200,
            ]
        ], 200);
    }

    public function getMaterial()
    {
        $res = Material::lainlain()->get();

        return (new AktivitasResource($res))->additional([
            'status' => [
                'message'   => '',
                'code'      => 200,
            ]
        ], 200);
    }

    public function getArea(Request $req, $id_gudang = '')
    {
        $user = $req->get('my_auth');

        $users = Users::findOrFail($user->id_user);
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

    public function getHousekeeper($id_rencana)
    {
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

    public function store(ApiRealisasiRequest $req, Realisasi $realisasi)
    {
        $id_rencana     = $req->input('id_rencana');

        $rencana = RencanaHarian::find($id_rencana);

        if (empty($rencana)) {
            return response()->json([
                'status' => [
                    'message'   => 'Rencana tidak ditemukan',
                    'code'      => 403,
                ]
            ], 403);
        }

        $temp_res = (new Realisasi)->where('id_rencana', $id_rencana)->first();
        (new Realisasi)->where('id_rencana', $id_rencana)->forceDelete();

        if (!empty($temp_res)) {
            (new RealisasiHousekeeper)->where('id_realisasi', $temp_res->id)->forceDelete();
            (new RealisasiMaterial)->where('id_realisasi', $temp_res->id)->forceDelete();
        }

        $housekeeper    = $req->input('housekeeper');
        $housekeeper    = array_values($housekeeper);

        $user = $req->get('my_auth');

        $realisasi->id_rencana  = $id_rencana;
        $realisasi->tanggal     = now();
        $realisasi->created_at  = now();
        $realisasi->created_by  = $user->id_user;
        $realisasi->save();

        if (!empty($housekeeper)) {
            foreach ($housekeeper as $key => $value) {
                $temp = array_values($req->input('area_housekeeper')[$key]);
                if (!empty($temp)) {
                    foreach ($temp as $row => $hey) {
                        $arr = [
                            'id_realisasi'  => $realisasi->id,
                            'id_tkbm'       => $value,
                            'id_area'       => $hey,
                        ];

                        (new RealisasiHousekeeper)->create($arr);
                    }
                }
            }
        }

        

        $housekeeper = RealisasiHousekeeper::where('id_realisasi', $realisasi->id)->get();

        $this->responseData = ['realisasi' => $realisasi, 'housekeeper' => $housekeeper, ];
        $this->responseCode = 200; 

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getRealisasiMaterial()
    {
        return AktivitasResource::collection(RealisasiMaterial::all())->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);
    }

    public function getShowRealisasiMaterial(RealisasiMaterial $realisasiMaterial)
    {
        $detail = MaterialTrans::where('id_realisasi_material', $realisasiMaterial->id)->get();

        $res = collect($realisasiMaterial);
        $res = $res->merge(['detail' => $detail]);


        return (new AktivitasResource($res))->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);
    }

    public function storeMaterial(Request $req, RealisasiMaterial $realisasiMaterial)
    {
        $tanggal    = $req->input('tanggal');
        $material   = $req->input('material');
        $tipe       = $req->input('tipe');
        $jumlah     = $req->input('jumlah');
        $panjang    = count($material);

        $realisasiMaterial->tanggal       = $tanggal;
        $realisasiMaterial->created_at    = now();

        $realisasiMaterial->save();

        for ($i = 0; $i < $panjang; $i++) {
            $arr = [
                'id_realisasi_material' => $realisasiMaterial->id,
                'id_material'           => $material[$i],
                'tanggal'               => now(),
                'tipe'                  => $tipe[$i],
                'jumlah'                => $jumlah[$i],
            ];

            (new MaterialTrans)->create($arr);
        }

        $data = MaterialTrans::where('id_realisasi_material', $realisasiMaterial->id)->get();

        $this->responseData = ['realisasi_material' => $realisasiMaterial, 'data' => $data];
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
