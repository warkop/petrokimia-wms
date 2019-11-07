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
        $id_alat_berat = strip_tags($req->input('id_alat_berat'));

        $res = AlatBeratHistory::select(
            'alat_berat_history.id', 
            'id_alat_berat_kerusakan', 
            \DB::raw('TO_CHAR(waktu, \'dd-mm-yyyy\') as tanggal'), 
            \DB::raw('TO_CHAR(waktu, \'H:i:s\') as pukul'), 
            'keterangan')
            ->leftJoin('alat_berat_kerusakan as abk', 'alat_berat_history.id_alat_berat_kerusakan', '=', 'abk.id')
            ->leftJoin('kerusakan_alat_berat as kab', 'abk.id', '=', 'kab.id_kerusakan')
            ->leftJoin('alat_berat_kat as abkat', 'kab.id_alat_berat_kat', '=', 'abkat.id')
            ->leftJoin('alat_berat as ab', 'abkat.id', '=', 'ab.id_kategori')
            
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(keterangan)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('TO_CHAR(waktu, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(\DB::raw('TO_CHAR(waktu, \'H:i:s\')'), 'ILIKE', '%' . $search . '%');
            });
        
        if (!empty($id_alat_berat)){
            $res = $res->where('ab.id', $id_alat_berat);
        }
        
        if (!empty($res)) {
            $obj =  AktivitasResource::collection($res->paginate(10))->additional([
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK
                ],
            ], Response::HTTP_OK);
        } else {
            $obj = [
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_NOT_FOUND,
                ],
            ];
        }


        return $obj;
    }

    public function detailHistory($id)
    {
        $res = AlatBeratHistory::find($id)->get();

        if (!empty($res)) {
            $obj =  AktivitasResource::collection($res)->additional([
                'url' => '{base_url}/watch/{foto}?token={access_token}&un={id_ab_history}&ctg=history&src={pics_url}',
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK
                ],
            ], Response::HTTP_OK);
        } else {
            $obj = [
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_NOT_FOUND,
                ],
            ];
        }

        return $obj;
    }
}
