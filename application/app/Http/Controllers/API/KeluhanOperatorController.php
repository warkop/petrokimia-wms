<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Karu;
use App\Http\Models\Keluhan;
use App\Http\Models\KeluhanOperator;
use App\Http\Models\Realisasi;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Requests\KeluhanOperatorRequest;
use App\Http\Resources\KeluhanGetOperatorResource;
use App\Http\Resources\KeluhanOperatorResource;
use Illuminate\Support\Facades\DB;

class KeluhanOperatorController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $obj =  KeluhanOperatorResource::collection(KeluhanOperator::select(
            'keluhan_operator.id',
            'keterangan',
            DB::raw('tk.nama as nama_operator'),
            DB::raw('k.nama as nama_keluhan')
        )
        ->leftJoin('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'id_operator')
        ->leftJoin('keluhan as k', 'k.id', '=', 'id_keluhan')
        ->where(function($query) use ($search) {
            $query->where(DB::raw('keterangan'), 'ILIKE', '%' . strtolower($search) . '%');
            $query->orWhere(DB::raw('tk.nama'), 'ILIKE', '%' . strtolower($search) . '%');
            $query->orWhere(DB::raw('k.nama'), 'ILIKE', '%' . strtolower($search) . '%');
        })
        ->orderBy('keluhan_operator.id', 'desc')->paginate(10))->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);

        return $obj;
    }

    public function show(KeluhanOperator $keluhanOperator)
    {
        $obj =  (new KeluhanOperatorResource($keluhanOperator))->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);

        return $obj;
    }

    public function getKeluhan()
    {
        $res = Keluhan::get();

        $data = [
            'data' => $res,
            'status' => [
                'message' => '',
                'code' => 200,
            ],
        ];

        return response()->json($data, 200);
    }

    public function getOperator()
    {
        $my_auth = request()->get('my_auth');
        $karu = Karu::find($my_auth->id_karu);
        $gudang = $this->getCheckerGudang();
        $rencanaHarian = RencanaHarian::where('id_gudang', $gudang->id)
        ->where('start_date', '<', date('Y-m-d H:i:s'))
        ->where('end_date', '>', date('Y-m-d H:i:s'))
        ->where('id_gudang', $gudang->id)
        ->orderBy('id', 'desc')
        ->first();

        if (empty($rencanaHarian)) {
            $data = [
                'data' => [],
                'status' => [
                    'message' => 'Rencana Kerja tidak ditemukan!',
                    'code' => 403,
                ],
            ];

            return response()->json($data, 403);
        }

        $realisasi = Realisasi::where('id_rencana', $rencanaHarian->id)->first();
        if (empty($realisasi)) {
            $res = RencanaTkbm::operator()->with('tkbm')->where('id_rencana', $rencanaHarian->id)->get();
            $data = [
                'data' => KeluhanGetOperatorResource::collection($res),
                'status' => [
                    'message' => '',
                    'code' => 200,
                ],
            ];
    
            return response()->json($data, 200);
        } else {
            $data = [
                'data' => [],
                'status' => [
                    'message' => 'Rencana Kerja sudah terealisasi!',
                    'code' => 403,
                ],
            ];

            return response()->json($data, 403);
        }
    }

    public function store(KeluhanOperatorRequest $req, KeluhanOperator $keluhanOperator)
    {
        $req->validated();

        $user = $req->get('my_auth');

        try {
            $keluhanOperator->keterangan    = $req->input('keterangan');
            $keluhanOperator->id_operator   = $req->input('id_operator');
            $keluhanOperator->id_keluhan    = $req->input('id_keluhan');
            $keluhanOperator->created_by    = $user->id_user;
            $keluhanOperator->created_at    = now();

            $keluhanOperator->save();

            $obj = [
                'data' => $keluhanOperator,
                'status' => [
                    'message' => 'Data berhasil disimpan!',
                    'code' => 201
                ],
            ];
            
            return $obj;
        } catch (\Exception $e) {
            $data = [
                'error_message' => $e->getMessage(),
                'status' => [
                    'message' => 'Ada kesalahan saat menyimpan ke database!',
                    'code' => 500
                ],
            ];

            return response()->json($data, 500);
        }
        
    }
}
