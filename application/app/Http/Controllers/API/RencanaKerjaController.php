<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\Karu;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Users;
use App\Http\Requests\ApiRencanaKerjaRequest;
use App\Http\Resources\AktivitasResource;
use App\Http\Resources\RencanaAreaTkbmResource;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RencanaKerjaController extends Controller
{
    public function index(Request $req)
    {
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $karu = Karu::find($res_user->id_karu);
        $res_gudang = Gudang::find($karu->id_gudang);

        $search = $req->input('search');

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
            DB::raw("
            CASE WHEN (SELECT id FROM realisasi WHERE id_rencana = rencana_harian.id ORDER BY id DESC LIMIT 1) IS NOT NULL
            THEN 'Done' ELSE 'Progress'
            END AS status")
        )
        ->where('id_gudang', $res_gudang->id)
        ->where(function($query) use ($search){
            $query->where(DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\')'), 'ILIKE', '%'.strtolower($search).'%');
            $query->orWhere(DB::raw('LOWER(CONCAT(\'shift \', id_shift))'), 'ILIKE', '%' . strtolower($search).'%');
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

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
        ->with('realisasi')
        ->get();

        $res = collect($rencanaHarian);
        $res = $res->merge(['list_admin_loket' => $adminLoket]);
        $res = $res->merge(['list_operator' => $operator]);
        $res = $res->merge(['list_checker' => $checker]);
        $res = $res->merge(['list_alat_berat' => $rencanaAlatBerat]);
        $res = $res->merge(['list_area_tkbm' => RencanaAreaTkbmResource::collection($rencanaAreaTkbm)]);


        return (new AktivitasResource($res))->additional([
            'status' => [
                'message'   => '',
                'code'      => 200,
            ]
        ], 200);
    }

    public function store(ApiRencanaKerjaRequest $req, $draft = 0, $id='')
    {
        $req->validated();

        if (!empty($id)) {
            $rencanaHarian = RencanaHarian::find($id);
            if (!empty($rencanaHarian) && $rencanaHarian->draft == 0) {
                $this->responseCode = 403;
                $this->responseMessage = 'Rencana Kerja tidak dalam status draft, Anda tidak bisa mengubahnya.';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
        } else {
            $rencanaHarian = new RencanaHarian;
        }

        $user = $req->get('my_auth');

        $res_user = Users::findOrFail($user->id_user);
        RencanaAreaTkbm::where('id_rencana', $rencanaHarian->id)->forceDelete();
        RencanaAlatBerat::where('id_rencana', $rencanaHarian->id)->forceDelete();
        RencanaTkbm::where('id_rencana', $rencanaHarian->id)->forceDelete();

        $karu = Karu::find($res_user->id_karu);
        $res_gudang = Gudang::find($karu->id_gudang);
        if (empty($res_gudang)) {
            $this->responseCode = 403;
            $this->responseMessage = 'Karu ini tidak terdaftar pada gudang manapun! Silahkan daftarkan terlebih dahulu.';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        $shift = ShiftKerja::findOrFail($req->input('id_shift'));
        if ($shift->mulai>$shift->akhir) {
            $rencanaHarian->end_date   = date('Y-m-d H:i:s', strtotime($shift->akhir . '+1 day'));
        } else {
            $rencanaHarian->end_date   = date('Y-m-d H:i:s', strtotime($shift->akhir));
        }

        //rencana harian
        $rencanaHarian->tanggal                = date('Y-m-d');
        $rencanaHarian->id_shift               = $req->input('id_shift');
        $rencanaHarian->id_gudang              = $res_gudang->id;
        $rencanaHarian->start_date             = date("Y-m-d H:i:s");
        $rencanaHarian->draft                  = $draft;
        $rencanaHarian->save();

        //rencana alat berat
        $alat_berat = $req->input('alat_berat');
        if (!empty($alat_berat)) {
            foreach ($alat_berat as $key => $value) {
                $arr = [
                    'id_rencana' => $rencanaHarian->id,
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
                    'id_rencana' => $rencanaHarian->id,
                    'id_tkbm' => $value['id_tkbm'],
                    'tipe' => 1,
                ];
                // $arrLoket[$value['id_tkbm']] = ['tipe' => 1];
                (new RencanaTkbm)->create($arr);
            }
        }

        $operator = $req->input('operator');
        if (!empty($operator)) {
            foreach ($operator as $key => $value) {
                $arr = [
                    'id_rencana' => $rencanaHarian->id,
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
                    'id_rencana' => $rencanaHarian->id,
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
                            'id_rencana' => $rencanaHarian->id,
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
    }

    public function jam()
    {
        // dd(new DateTime());
        $shift = ShiftKerja::find(3);
        $date1 = new DateTime($shift->mulai);
        $date2 = new DateTime($shift->akhir);

        $diff = $date2->diff($date1);

        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);

        if ($shift->mulai > $shift->akhir) {
            echo date('Y-m-d H:i:s', strtotime($shift->akhir. '+1 day'));
        } else {
            echo date('Y-m-d H:i:s', strtotime($shift->akhir));
        }

        // dd($shift->mulai> $shift->akhir);
        // echo date("Y-m-d H:i:s", strtotime('+'.$hours.' hours'));
    }

    public function getShift()
    {
        $res = ShiftKerja::orderBy('id')->get();

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
            $karu = Karu::find($users->id_karu);
            $gudang = Gudang::find($karu->id_gudang);
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
