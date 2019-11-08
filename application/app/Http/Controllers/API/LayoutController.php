<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Area;
use App\Http\Resources\AktivitasResource;
use Illuminate\Http\Response;

class LayoutController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $res = Area::select(
                'area.id', 
                'area.nama as nama_area', 
                'g.nama as nama_gudang', 
                'tipe_gudang',
                'kapasitas',
                'tipe as tipe_area',
                \DB::raw('
                    CASE
                        WHEN tipe_gudang=1 THEN \'Internal\'
                    ELSE \'Eksternal\'
                END AS text_tipe_gudang'),
                \DB::raw('
                    CASE
                        WHEN tipe=1 THEN \'Indoor\'
                    ELSE \'Outdoor\'
                END AS text_tipe_area')
            )
            ->join('gudang as g', 'area.id_gudang', '=', 'g.id')
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(area.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('LOWER(g.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->orderBy('area.created_at', 'asc')            
            ->withoutGlobalScopes()
            ->paginate(10);

        $obj =  AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }
}
