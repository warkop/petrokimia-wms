<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Requests\AktivitasKeluhanGpRequest;
use App\Http\Resources\MaterialTransResource;
use Illuminate\Http\Request;

class PenerimaanGpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Penerimaan GP';
        return view('penerimaan-gp.grid', $data);
    }

    public function json(Request $req)
    {
        $models = new AktivitasHarian();

        $numbcol = $req->get('order');
        $columns = $req->get('columns');

        $echo    = $req->get('draw');
        $start   = $req->get('start');
        $perpage = $req->get('length');

        $search  = $req->get('search');
        $search  = $search['value'];
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search  = preg_replace($pattern, '', $search);

        $gudang     = $req->get('gudang');
        $shift      = $req->get('shift');
        $sort = $numbcol[0]['dir'];
        $field = $columns[$numbcol[0]['column']]['data'];

        $condition = [
            'id_gudang' => $gudang ?? '',
            'id_shift'  => $shift ?? '',
        ];

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $result = $models->jsonGridGp($start, $perpage, $search, false, $sort, $field, $condition);
            $total  = $models->jsonGridGp($start, $perpage, $search, true, $sort, $field, $condition);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array('sEcho' => $echo, 'iTotalRecords' => $total, 'iTotalDisplayRecords' => $total, 'aaData' => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function getProduk(Request $req, $id_aktivitas_harian=false)
    {
        $search = $req->input('q');
        $keluhan = '';
        if ($id_aktivitas_harian) {
            $keluhan = AktivitasKeluhanGp::where('id_aktivitas_harian', $id_aktivitas_harian)->get();
        }
        // $data = Material::where('nama', 'ILIKE', '%'.strtolower($search).'%')->where('kategori', 1)->orderBy('nama', 'asc')->get();
        
        $data = MaterialTrans::
        with('material')
        ->whereHas('aktivitasHarian.aktivitas', function($query) {
            $query->whereNotNull('pengiriman');
        })
        ->whereHas('material', function($query) use($search) {
            $query->where('kategori', 1);
            $query->where('nama', 'ILIKE', '%' . strtolower($search) . '%');
        })
        ->get();
        
        return response()->json(['data' => $data, 'keluhan' => $keluhan], 200);
    }

    public function dataKeluhan(Request $req, $id_aktivitas_harian)
    {
        $res = AktivitasKeluhanGp::where('id_aktivitas_harian', $id_aktivitas_harian);

        return response()->json(['data' => $res], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AktivitasKeluhanGpRequest $req, AktivitasHarian $aktivitasHarian)
    {
        $req->validated();

        if (!empty($req->input('produk'))) {
            $produk = $req->input('produk');
            $arr = [];
            AktivitasKeluhanGp::where('id_aktivitas_harian', $aktivitasHarian->id)->delete();
            foreach ($produk as $key) {
                $temp = [
                    'id_material'   => $req->input('produk')[$key],
                    'jumlah'        => $req->input('jumlah')[$key],
                    'keluhan'       => $req->input('keluhan')[$key],
                ];
                array_push($arr, new AktivitasKeluhanGp($temp));
            }

            $aktivitasHarian->aktivitasKeluhanGp()->saveMany($arr);
        }

        return response()->json($req->all(), 200);
    }

    public function approve(AktivitasHarian $aktivitasHarian)
    {
        if ($aktivitasHarian->approve == null) {
            $aktivitasHarian->approve =date('Y-m-d H:i:s');
            $aktivitasHarian->save();
            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Data sudah disetujui!';

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AktivitasHarian $aktivitasHarian)
    {
        $data['title'] = 'Detail Penerimaan GP';
        $data['aktivitasHarian'] = $aktivitasHarian;
        $data['id_aktivitas_harian'] = $aktivitasHarian->id;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();
        // $res = AreaStok::select(
        //     'id_area',
        //     'nama',
        //     'jumlah'
        // )
        // ->leftJoin('area', 'area.id', '=', 'area_stok.id_area');

        // $res = AreaStok::with('area')->get();

        $produk = MaterialTrans::with('material')
        ->where('id_aktivitas_harian', $aktivitasHarian->id)
        ->whereNotNull('status_produk')
        ->get();
        $data['produk'] = MaterialTransResource::collection($produk);
        $pallet = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_pallet')->get();
        $data['pallet'] = $pallet;
        $data['id_gudang'] = $aktivitasHarian->id_gudang;

        $data['list_produk'] = Material::produk()->get();
        return view('penerimaan-gp.detail', $data);
    }

    public function getArea($id_gudang, $id_material, $id_aktivitas_harian)
    {
        $areaStok = Area::with('areaStok', 'areaStok.materialTrans')
        ->whereHas('areaStok', function($query) use ($id_material) {
            $query->where('id_material', $id_material);
        })
        ->whereHas('areaStok.materialTrans', function($query) use ($id_aktivitas_harian) {
            $query->where('id_aktivitas_harian', $id_aktivitas_harian);
        })
        ->where('id_gudang', $id_gudang)
        ->orderBy('nama')
        ->get();
        return response()->json($areaStok, 200);
    }
}
