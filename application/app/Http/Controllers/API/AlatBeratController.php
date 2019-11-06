<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\AlatBerat;
use App\Http\Models\AlatBeratHistory;
use App\Http\Resources\AktivitasResource;
use Illuminate\Http\Response;

class AlatBeratController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $res = AlatBerat::
            select('alat_berat.id', 'nomor_lambung', 'nama')
            ->leftJoin('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('LOWER(nomor_lambung)'), 'ILIKE', '%' . strtolower($search) . '%');
            })->paginate(10);

        $obj =  AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        // $obj = [
        //     'data' => $res,
        //     'status' => [
        //         'message' => '',
        //         'code' => Response::HTTP_OK
        //     ]
        // ];
        
        // $obj =  AktivitasResource::collection((new AlatBerat)
        // ->kategori()
        // ->where(function ($where) use ($search) {
        //     $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        // })->paginate(10))->additional([
        //     'status' => [
        //         'message' => '',
        //         'code' => Response::HTTP_OK
        //     ],
        // ], Response::HTTP_OK);

        return $obj;
    }

    public function history(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $res = AlatBeratHistory::select(
            'alat_berat_history.id', 
            'id_alat_berat_kerusakan', 
            \DB::raw('TO_CHAR(waktu, \'dd-mm-yyyy\') as tanggal'), 
            \DB::raw('TO_CHAR(waktu, \'H:i:s\') as pukul'), 
            'keterangan')
            ->leftJoin('alat_berat as ab', 'alat_berat_history.id_alat_berat_kerusakan', '=', 'ab.id')
            ->leftJoin('kerusakan_alat_berat as kab', 'kab.id_alat_berat_kerusakan', '=', 'ab.id')
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(keterangan)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('TO_CHAR(waktu, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(\DB::raw('TO_CHAR(waktu, \'H:i:s\')'), 'ILIKE', '%' . $search . '%');
            })->paginate(10);

        $obj =  AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }
}
