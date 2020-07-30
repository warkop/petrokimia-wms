<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasKelayakanFoto;
use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Requests\AktivitasKeluhanGpRequest;
use App\Http\Resources\MaterialTransResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function test(Request $request)
    {
        var_dump($request->image_keluhan);
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
        
        $data = MaterialTrans::distinct('id_material')
        ->select(
            'id_material',
            'nama'
        )
        ->join('material', 'material.id', '=', 'id_material')
        ->where(function($query) use($search) {
            $query->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })
        ->where('id_aktivitas_harian', $id_aktivitas_harian)
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
            if ($req->input('produk')) {
                foreach ($produk as $key => $value) {
                    $foto = $req->file('foto');
                    
                    $temp = [
                        'id_material'   => $req->input('produk')[$key],
                        'jumlah'        => $req->input('jumlah')[$key],
                        'keluhan'       => $req->input('keluhan')[$key],
                    ];

                    if (!empty($foto)) {
                        if ($foto[$key]->isValid()) {
                            $foto[$key]->storeAs('public/keluhan_gp/'.$aktivitasHarian->id.'/', $foto[$key]->getClientOriginalName());

                            $temp['foto'] = $foto[$key]->getClientOriginalName();
                        }
                    }
                    array_push($arr, new AktivitasKeluhanGp($temp));
                }
    
                $aktivitasHarian->aktivitasKeluhanGp()->saveMany($arr);
            }
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
    public function show($id)
    {
        $aktivitasHarian = AktivitasHarian::where('id', $id)
        ->whereHas('aktivitas', function($query){
            $query->whereNotNull('pengiriman');
            $query->whereNotNull('pengaruh_tgl_produksi');
        })->firstOrFail();
        $data['title'] = 'Detail Penerimaan GP';
        $data['aktivitasHarian'] = $aktivitasHarian;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();

        $produk = MaterialTrans::select(
            'material_trans.id_material',
            'material.nama as nama_material',
            'area_stok.id_area',
            'material_trans.tipe',
            DB::raw('SUM(material_trans.jumlah) as jumlah')
        )
        ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
        ->leftJoin('area_stok', 'area_stok.id', '=', 'material_trans.id_area_stok')
        ->where('id_aktivitas_harian', $aktivitasHarian->id)
        ->whereNotNull('status_produk')
        ->groupBy('material_trans.id_material', 'material.nama', 'area_stok.id_area', 'material_trans.tipe')
        ->get();
        $data['produk'] = $produk;
        $pallet = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_pallet')->get();
        $data['pallet'] = $pallet;
        $data['fotoKelayakanBefore'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 1)->get();
        $data['fotoKelayakanAfter'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 2)->get();

        $data['list_produk'] = Material::produk()->get();
        return view('penerimaan-gp.detail', $data);
    }

    public function getListKeluhanGP($id)
    {
        $aktivitasKeluhanGp = AktivitasKeluhanGp::with('material')
        ->where('id_aktivitas_harian', $id)
        ->get();
        $this->responseCode = 200;
        $this->responseData = $aktivitasKeluhanGp;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getArea($id_gudang, $id_material, $id_aktivitas_harian)
    {
        $areaStok = MaterialTrans::select(
            'material_trans.id_material',
            'area_stok.id_area',
            'area.nama as nama_area',
            'area_stok.tanggal',
            'material_trans.jumlah'
        )
        ->leftJoin('area_stok', 'area_stok.id', '=', 'material_trans.id_area_stok')
        ->leftJoin('area', 'area.id', '=', 'area_stok.id_area')
        ->where('id_aktivitas_harian', $id_aktivitas_harian)
        ->where('material_trans.id_material', $id_material)
        ->get();
        return response()->json($areaStok, 200);
    }

    public function print($id)
    {
        $aktivitasHarian = AktivitasHarian::with('checker')->where('id', $id)
            ->whereHas('aktivitas', function ($query) {
                $query->whereNotNull('pengiriman');
                $query->whereNotNull('pengaruh_tgl_produksi');
                $query->withoutGlobalScopes();
            })->firstOrFail();
        $data['title'] = 'Cetak Penerimaan GP';
        $data['aktivitasHarian'] = $aktivitasHarian;
        $data['aktivitasFoto'] = AktivitasFoto::withoutGlobalScopes()->where('id_aktivitas_harian', $aktivitasHarian->id)->get();

        $produk = MaterialTrans::select(
            'material_trans.id_material',
            'material.nama as nama_material',
            'area.nama as nama_area',
            'area_stok.id_area',
            'material_trans.tipe',
            'area_stok.tanggal',
            'material_trans.jumlah'
        )
            ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
            ->leftJoin('area_stok', 'area_stok.id', '=', 'material_trans.id_area_stok')
            ->leftJoin('area', 'area_stok.id_area', '=', 'area.id')
            ->where('id_aktivitas_harian', $aktivitasHarian->id)
            ->whereNotNull('status_produk')
            ->get();
        $data['produk'] = $produk;
        $pallet = MaterialTrans::with('material')->where('id_aktivitas_harian', $aktivitasHarian->id)->whereNotNull('status_pallet')->get();
        $data['pallet'] = $pallet;
        $data['fotoKelayakanBefore'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 1)->get();
        $data['fotoKelayakanAfter'] = AktivitasKelayakanFoto::where('id_aktivitas_harian', $aktivitasHarian->id)->where('jenis', 2)->get();

        return view('penerimaan-gp.cetak', $data);
    }
}
