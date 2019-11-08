<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\JenisFoto;
use App\Http\Models\Material;
use App\Http\Requests\ApiAktivitasRequest;
use App\Http\Resources\AktivitasResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AktivitasController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $obj =  AktivitasResource::collection(Aktivitas::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->paginate(10))->additional([
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function getMaterial(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Material::produk()->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }
    
    public function getPallet(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Material::pallet()->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getGudang(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Gudang::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getArea(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Area::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getAlatBerat(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = AlatBerat::
        leftJoin('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
        ->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            $where->orWhere(\DB::raw('LOWER(nomor_lambung)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function store(Request $req)
    {
        // $req->validated();

        $models = new AktivitasHarian;

        $arr = [
            'id_aktivitas'      => $req->input('id_aktivitas'),
            'id_gudang'         => $req->input('id_gudang'),
            'id_karu'           => $req->input('id_karu'),
            'id_shift'          => $req->input('id_shift'),
            'ref_number'        => $req->input('ref_number'),
            'id_area'           => $req->input('id_area'),
            'id_alat_berat'     => $req->input('id_alat_berat'),
            'ttd'               => $req->input('ttd'),
            'sistro'            => $req->input('sistro'),
            'approve'           => $req->input('approve'),
            'kelayakan_before'  => $req->input('kelayakan_before'),
            'kelayakan_after'   => $req->input('kelayakan_after'),
            'dikembalikan'      => $req->input('dikembalikan'),
            // 'created_by'        => $req->input('dikembalikan'),
            // 'created_at'        => now(),
        ];

        $aktivitas = $models->create($arr);

        return (new AktivitasResource($aktivitas))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_CREATED,
            ]
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        try {
            $aktivitas = Aktivitas::findOrFail($id);
            return (new AktivitasResource($aktivitas))->additional([
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK,
                ]
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'data' => null,
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_NOT_FOUND
                ]
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function getJenisFoto()
    {
        $resource = JenisFoto::get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }
}
