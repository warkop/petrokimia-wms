<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Users;
use App\Http\Requests\ApiRencanaKerjaRequest;
use App\Http\Resources\AktivitasResource;
use Illuminate\Http\Response;

class RencanaKerjaController extends Controller
{
    public function index(Request $req)
    {
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $res_gudang = Gudang::where('id_karu', $res_user->id_karu)->first();
        if (empty($res_gudang)) {
            $data = [
                'status' => [
                    'message' => 'Karu ini tidak terdaftar pada gudang manapun! Silahkan daftarkan terlebih dahulu',
                    'code' => 403
                ],
            ];    

            return response()->json($data, 403);
        }

        $data = RencanaHarian::select(
            '*',
            \DB::raw("
            CASE WHEN (SELECT id FROM realisasi where id_rencana = rencana_harian.id) IS NOT NULL
            THEN 'Done' ELSE 'Progress'
            END AS status")
        )
        ->where('id_gudang', $res_gudang->id)->orderBy('id', 'desc')->paginate(10);

        return AktivitasResource::collection($data)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        $rencanaHarian = RencanaHarian::findOrFail($id);
        
        $adminLoket = RencanaTkbm::select(
            'id_rencana',
            'id_tkbm',
            'job_desk_id',
            'nama'
        )
        ->join('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'rencana_tkbm.id_tkbm')
        ->where('id_rencana', $rencanaHarian->id)
        ->where('job_desk_id', 1)
        ->get();
        
        $operator = RencanaTkbm::select(
            'id_rencana',
            'id_tkbm',
            'job_desk_id',
            'nama'
        )
        ->join('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'rencana_tkbm.id_tkbm')
        ->where('id_rencana', $rencanaHarian->id)
        ->where('job_desk_id', 2)
        ->get();
        
        $checker = RencanaTkbm::select(
            'id_rencana',
            'id_tkbm',
            'job_desk_id',
            'nama'
        )
        ->join('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'rencana_tkbm.id_tkbm')
        ->where('id_rencana', $rencanaHarian->id)
        ->where('job_desk_id', 3)
        ->get();

        $rencanaAlatBerat = RencanaAlatBerat::select(
            'id_rencana',
            'id_alat_berat',
            'ab.nomor_lambung',
            'abk.nama as kategori'
        )
        ->join('alat_berat as ab', 'ab.id', '=', 'rencana_alat_berat.id_alat_berat')
        ->join('alat_berat_kat as abk', 'abk.id', '=', 'ab.id_kategori')
        ->where('id_rencana', $rencanaHarian->id)
        ->get();

        $rencanaAreaTkbm = RencanaAreaTkbm::select(
            'id_rencana',
            'id_area',
            'id_tkbm',
            'tk.nama',
            'area.nama as nama_area'
        )
        ->join('area', 'area.id', '=', 'rencana_area_tkbm.id_area')
        ->join('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'rencana_area_tkbm.id_tkbm')
        ->where('id_rencana', $rencanaHarian->id)
        ->where('job_desk_id', 4)
        ->get();

        $res = collect($rencanaHarian);
        $res = $res->merge(['list_admin_loket' => $adminLoket]);
        $res = $res->merge(['list_operator' => $operator]);
        $res = $res->merge(['list_checker' => $checker]);
        $res = $res->merge(['list_alat_berat' => $rencanaAlatBerat]);
        $res = $res->merge(['list_area_tkbm' => $rencanaAreaTkbm]);


        return (new AktivitasResource($res))->additional([
            'status' => [
                'message'   => '',
                'code'      => 200,
            ]
        ], 200);
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

    public function getShift()
    {
        $res = ShiftKerja::get();

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
        $alat_berat = new AlatBerat();
        $res = $alat_berat->getWithRelation();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
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
}
