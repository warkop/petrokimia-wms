<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Karu;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Resources\AktivitasResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LayoutController extends Controller
{
    private function getCheckerGudang()
    { //untuk memperoleh informasi checker ini sekarang berada di gudang mana
        if (request()->get('my_auth')->role == 3) {
            $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
                ->where('id_tkbm', request()->get('my_auth')->id_tkbm)
                ->orderBy('rencana_harian.id', 'desc')
                ->take(1)->first();

            if (empty($rencana_tkbm)) {
                $this->responseCode = 500;
                $this->responseMessage = 'Checker tidak terdaftar pada rencana harian apapun!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
            $rencana_harian = RencanaHarian::findOrFail($rencana_tkbm->id_rencana);
            $gudang = Gudang::findOrFail($rencana_harian->id_gudang);
        } else if (request()->get('my_auth')->role == 5) {
            $karu   = Karu::find(request()->get('my_auth')->id_karu);
            $gudang = Gudang::find($karu->id_gudang);
        } else {
            return false;
        }

        return $gudang;
    }

    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $gudang = $this->getCheckerGudang();
        $res = Area::select(
                'area.id', 
                'area.nama as nama_area', 
                'g.nama as nama_gudang', 
                'tipe_gudang',
                'kapasitas',
                'tipe as tipe_area',
                DB::raw('(SELECT SUM(jumlah) FROM area_stok WHERE area_stok.id_area = area.id) AS total'),
                DB::raw('
                    CASE
                        WHEN tipe_gudang=1 THEN \'Internal\'
                    ELSE \'Eksternal\'
                END AS text_tipe_gudang'),
                DB::raw('
                    CASE
                        WHEN tipe=1 THEN \'Indoor\'
                    ELSE \'Outdoor\'
                END AS text_tipe_area')
            )
            ->join('gudang as g', 'area.id_gudang', '=', 'g.id')
            ->where('id_gudang', $gudang->id)
            ->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(area.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(g.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->orderBy('area.created_at', 'asc')           
            ->where(function($query){
                $query->where('area.end_date', null)->orWhere('area.end_date', '>=', now());
            })
            ->where(function ($query) {
                $query->where('g.end_date', null)->orWhere('g.end_date', '>=', now());
            })
            ->withoutGlobalScopes()
            ->orderBy('area.nama', 'asc')
            ->paginate(10);

        $listPallet = DB::table('gudang_stok')
        ->select(
            'gudang_stok.*'
        )
        ->join('material', 'material.id', '=', 'gudang_stok.id_material')
        ->where('kategori', 2)
        ->where('id_gudang', $gudang->id)
        ->orderBy('status')
        ->get();

        $stok       = 0;
        $terpakai   = 0;
        $kosong     = 0;
        $rusak      = 0;

        foreach ($listPallet as $key) {
            if ($key->status == 1) {
                $stok = $stok + $key->jumlah;
            }
            if ($key->status == 2) {
                $terpakai = $terpakai + $key->jumlah;
            }
            if ($key->status == 3) {
                $kosong = $kosong + $key->jumlah;
            }
            if ($key->status == 4) {
                $rusak = $rusak + $key->jumlah;
            }
        }

        $produkNormal = 0;
        $produkRusak = 0;

        $produk = AreaStok::select('*')
        ->leftJoin('area', 'area.id', '=', 'area_stok.id_area')
        ->where('id_gudang', $gudang->id)
        ->get();

        foreach ($produk as $key) {
            if ($key->status == 1) {
                $produkNormal += $key->jumlah;
            } else {
                $produkRusak += $key->jumlah;
            }
        }

        $obj =  AktivitasResource::collection($res)->additional([
            'pallet' => [
                'stok'      => $stok,
                'terpakai'  => $terpakai,
                'kosong'    => $kosong,
                'rusak'     => $rusak,
            ],
            'produk' => [
                'total'     => $produkNormal+$produkRusak,
                'normal'    => $produkNormal,
                'rusak'     => $produkRusak,
            ],
            'status' => [
                'message'   => '',
                'code'      => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function detail($id_area)
    {
        $res = AreaStok::select(
            'area.id',
            'id_material',
            'area.nama as nama_area',
            'material.nama as nama_material',
            'area_stok.tanggal',
            'area_stok.jumlah',
            'area.kapasitas',
            'area_stok.status'
        )
        ->leftJoin('material', 'area_stok.id_material', '=', 'material.id')
        ->leftJoin('area', 'area_stok.id_area', '=', 'area.id')
        ->where('id_area',$id_area)
        ->get();

        $obj =  AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function detailPallet($status_pallet)
    {
        $gudang = $this->getCheckerGudang();
        $res = GudangStok::select(
            'gudang_stok.id',
            'id_material',
            'material.nama as nama_material',
            'gudang_stok.jumlah',
            'gudang_stok.status'
        )
            ->leftJoin('material', 'gudang_stok.id_material', '=', 'material.id')
            ->where('id_gudang', $gudang->id)
            ->where('status', $status_pallet)
            ->get();

        $obj =  AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }
}
