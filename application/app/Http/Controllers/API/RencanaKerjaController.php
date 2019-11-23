<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Gudang;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Users;
use App\Http\Requests\ApiRencanaKerjaRequest;
use Illuminate\Http\Response;

class RencanaKerjaController extends Controller
{
    public function index(Request $req)
    {
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $res_gudang = Gudang::where('id_karu', $res_user->id_karu)->first();

        $data = RencanaHarian::where('id_gudang', $res_gudang->id)->paginate(10);

        return AktivitasResource::collection($data)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);
    }

    public function store(ApiRencanaKerjaRequest $req)
    {
        $req->validated();

        $user = $req->get('my_auth');

        $res_user = Users::findOrFail($user->id_user);

        if ($res_user->role_id == 5) {
            if (!empty($id)) {
                $rencana_harian = RencanaHarian::find($req->input('id'));

                RencanaAlatBerat::where('id_rencana', $rencana_harian->id)->forceDelete();
                RencanaTkbm::where('id_rencana', $rencana_harian->id)->forceDelete();
                RencanaAreaTkbm::where('id_rencana', $rencana_harian->id)->forceDelete();
            } else {
                $rencana_harian = new RencanaHarian();
            }

            $res_gudang = Gudang::where('id_karu', $res_user->id_karu)->first();
            if (empty($res_gudang)) {
                $this->responseCode = 403;
                $this->responseMessage = 'Karu ini tidak terdaftar pada gudang manapun! Silahkan daftarkan terlebih dahulu.';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
            //rencana harian
            $rencana_harian->tanggal                = date('Y-m-d');
            $rencana_harian->id_shift               = $req->input('id_shift');
            $rencana_harian->start_date             = date('Y-m-d');
            $rencana_harian->created_at             = date('Y-m-d H:i:s');
            $rencana_harian->id_gudang              = $res_gudang->id;
            $rencana_harian->save();

            //rencana alat berat
            $alat_berat = $req->input('alat_berat');
            if (!empty($alat_berat)) {
                foreach ($alat_berat as $key => $value) {
                    $arr = [
                        'id_rencana' => $rencana_harian->id,
                        'id_alat_berat' => $value['id_alat_berat'],
                    ];

                    (new RencanaAlatBerat)->create($arr);
                }
            }

            //rencana tkbm
            $admin_loket = $req->input('admin_loket');
            if (!empty($admin_loket)) {
                foreach ($admin_loket as $key => $value) {
                    $arr = [
                        'id_rencana' => $rencana_harian->id,
                        'id_tkbm' => $value['id_tkbm'],
                        'tipe' => 1,
                    ];
    
                    (new RencanaTkbm)->create($arr);
                }
            }

            $operator = $req->input('operator');
            if (!empty($operator)) {
                foreach ($operator as $key => $value) {
                    $arr = [
                        'id_rencana' => $rencana_harian->id,
                        'id_tkbm' => $value['id_tkbm'],
                        'tipe' => 2,
                    ];
    
                    (new RencanaTkbm)->create($arr);
                }
            }

            $checker = $req->input('checker');
            if (!empty($checker)) {
                foreach ($checker as $key => $value) {
                    $arr = [
                        'id_rencana' => $rencana_harian->id,
                        'id_tkbm' => $value['id_tkbm'],
                        'tipe' => 3,
                    ];
    
                    (new RencanaTkbm)->create($arr);
                }
            }

            //rencana area tkbm
            $housekeeper = $req->input('housekeeper');
            if (!empty($housekeeper)) {
                foreach ($housekeeper as $key => $value) {
                    $area = $value['area'];
                    if (!empty($area)) {
                        foreach ($area as $row => $hey) {
                            $id_area = $hey['id_area'];
                            $arr = [
                                'id_rencana' => $rencana_harian->id,
                                'id_tkbm' => $value['id_tkbm'],
                                'id_area' => $id_area,
                            ];

                            (new RencanaAreaTkbm)->create($arr);
                        }
                    }
                }
            }

            $this->responseData = $req->all();
            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Hanya Karu yang diizinkan untuk menyimpan Rencana Kerja!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
    }
}
