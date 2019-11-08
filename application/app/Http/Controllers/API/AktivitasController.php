<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasFoto;
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

    public function store(Request $req, AktivitasHarian $aktivitas)
    {
        // $req->validated();

        // $models = new AktivitasHarian;
        $user = $req->get('my_auth');

        // $id = $req->input('id');
        // if (!empty($id)) {
        //     $aktivitas = AktivitasHarian::find($id);
        // }
        
        $aktivitas->id_aktivitas      = $req->input('id_aktivitas');
        $aktivitas->id_gudang         = $req->input('id_gudang');
        $aktivitas->id_karu           = $req->input('id_karu');
        $aktivitas->id_shift          = $req->input('id_shift');
        $aktivitas->ref_number        = $req->input('ref_number');
        $aktivitas->id_area           = $req->input('id_area');
        $aktivitas->id_alat_berat     = $req->input('id_alat_berat');
        $aktivitas->ttd               = $req->input('ttd');
        $aktivitas->sistro            = $req->input('sistro');
        $aktivitas->approve           = $req->input('approve');
        $aktivitas->kelayakan_before  = $req->input('kelayakan_before');
        $aktivitas->kelayakan_after   = $req->input('kelayakan_after');
        $aktivitas->dikembalikan      = $req->input('dikembalikan');
        $aktivitas->created_by        = $user->id_user;
        $aktivitas->created_at        = now();

        $saved = $aktivitas->save();

        if ($saved) {
            $foto = $req->file('foto');
            $foto_jenis = $req->input('foto_jenis');
            $lat = $req->input('lat');
            $lng = $req->input('lng');
            $panjang = count($foto);
            (new AktivitasFoto)->where('id_aktivitas_harian', '=', $aktivitas->id)->delete();
            \Storage::deleteDirectory('/public/aktivitas_harian/' . $aktivitas->id);

            for ($i = 0; $i < $panjang; $i++) {
                if ($foto[$i]->isValid()) {
                    $aktivitasFoto = new AktivitasFoto;

                    $foto[$i]->storeAs('/public/aktivitas_harian/' . $aktivitas->id, $foto[$i]->getClientOriginalName());

                    $arrayFoto = [
                        'id_aktivitas_harian'       => $aktivitas->id,
                        'id_foto_jenis'             => $foto_jenis[$i],
                        'foto'                      => $foto[$i]->getClientOriginalName(),
                        'size'                      => $foto[$i]->getSize(),
                        'lat'                       => $lat[$i],
                        'lng'                       => $lng[$i],
                        'created_by'                => $user->id_user,
                        'created_at'                => now(),
                    ];

                    $aktivitasFoto->create($arrayFoto);
                }
            }

            $foto = AktivitasFoto::where('id_aktivitas_harian', $aktivitas->id)->get();

            return (new AktivitasResource($aktivitas))->additional([
                'foto' => $foto,
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_CREATED,
                ]
            ], Response::HTTP_CREATED);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Gagal menyimpan aktivitas!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
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
