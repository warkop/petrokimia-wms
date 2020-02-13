<?php

namespace App\Http\Controllers;

use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use App\Http\Models\MaterialAdjustment;
use App\Http\Models\MaterialTrans;
use App\Http\Models\ShiftKerja;
use App\Http\Requests\MaterialAdjusmentRequest;
use App\Http\Requests\MaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialAdjustmentController extends Controller
{
    public function index($id)
    {
        $data['title'] = 'Stock Adjustment';
        $data['id_gudang'] = $id;
        $data['shift'] = ShiftKerja::get();
        $gudang = Gudang::findOrFail($id);
        if (!empty($gudang)) {
            $data['gudang'] = $gudang;
            return view('stock-adjustment.grid', $data);
        } else {
            abort(404);
        }
    }

    public function json(Request $req, $id_gudang)
    {
        $models = new MaterialAdjustment();

        $numbcol = $req->get('order');
        $columns = $req->get('columns');

        $echo    = $req->get('draw');
        $start   = $req->get('start');
        $perpage = $req->get('length');

        $search  = $req->get('search');
        $search  = $search['value'];
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search  = preg_replace($pattern, '', $search);

        $sort = $numbcol[0]['dir'];
        $field = $columns[$numbcol[0]['column']]['data'];

        $condition = '';

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $result = $models->jsonGrid($start, $perpage, $search, false, $sort, $field, $condition, $id_gudang);
            $total  = $models->jsonGrid($start, $perpage, $search, true, $sort, $field, $condition, $id_gudang);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function store($id_gudang='', MaterialAdjusmentRequest $req)
    {
        $gudang = Gudang::findOrFail($id_gudang);
        $req->validate();

        $id = $req->input('id');
        $shift_id      = $req->input('shift_id');
        if (!empty($id)) {
            $materialAdjustment = MaterialAdjustment::find($id);
            MaterialTrans::where('id_adjustment', $id)->truncate();
        } else {
            $materialAdjustment = new MaterialAdjustment;
        }

        //material adjustment
        $materialAdjustment->tanggal    = date('Y-m-d', strtotime($req->input('tanggal')));
        $materialAdjustment->id_gudang  = $id_gudang;
        $materialAdjustment->shift      = $shift_id;
        $materialAdjustment->save();

        //material trans
        $produk        = $req->input('produk');
        $area          = $req->input('area');
        $tanggal       = $req->input('tanggal_produksi');
        $action_produk = $req->input('action_produk');
        $produk_jumlah = $req->input('produk_jumlah');
        $produk_alasan = $req->input('produk_alasan');
        if (!empty($produk)) {
            $panjang          = count($produk);
            $produk           = array_values((array)$produk);
            $area             = array_values((array)$area);
            $tanggal          = array_values((array)$tanggal);
            $action_produk    = array_values((array)$action_produk);
            $produk_jumlah    = array_values((array)$produk_jumlah);
            $produk_alasan    = array_values((array)$produk_alasan);

            for ($i = 0; $i < $panjang; $i++) {
                $areaStok = AreaStok::where('id_area', $area[$i])->where('id_material', $produk[$i])->where('tanggal', date('Y-m-d', strtotime($tanggal[$i])))->first();
                if (empty($areaStok)) {
                    $areaStok = new AreaStok;
                    $areaStok->id_area      = $area[$i];
                    $areaStok->id_material  = $produk[$i];
                    $areaStok->tanggal      = date('Y-m-d', strtotime($tanggal[$i]));
                    $areaStok->jumlah       = $produk_jumlah[$i];
                } else {
                    if ($action_produk[$i] == 1) {
                        $areaStok->jumlah         = $areaStok->jumlah - $produk_jumlah[$i];
                    } else if ($action_produk[$i] == 2) {
                        $areaStok->jumlah         = $areaStok->jumlah + $produk_jumlah[$i];
                    }
                }
                
                $areaStok->save();

                $materialTrans = new MaterialTrans;
                $materialTrans->id_adjustment   = $materialAdjustment->id;
                $materialTrans->id_material     = $produk[$i];
                $materialTrans->tipe            = $action_produk[$i];
                $materialTrans->jumlah          = $produk_jumlah[$i];
                $materialTrans->alasan          = $produk_alasan[$i];
                $materialTrans->id_area_stok    = $areaStok->id;
                $materialTrans->tanggal         = date('Y-m-d', strtotime($tanggal[$i]));
                $materialTrans->status_produk   = 1;
                $materialTrans->id_area         = $area[$i];
                $materialTrans->shift_id        = $shift_id;
                $materialTrans->save();
            }
        }

        $pallet = $req->input('pallet');
        $action_pallet = $req->input('action_pallet');
        $pallet_jumlah = $req->input('pallet_jumlah');
        $pallet_alasan = $req->input('pallet_alasan');

        if (!empty($pallet)) {
            $panjang          = count($pallet);
            $pallet           = array_values((array)$pallet);
            $action_pallet    = array_values((array)$action_pallet);
            $pallet_jumlah    = array_values((array)$pallet_jumlah);
            $pallet_alasan    = array_values((array)$pallet_alasan);

            for ($i = 0; $i < $panjang; $i++) {
                $gudangStok = GudangStok::where('id_gudang', $gudang->id)->where('id_material', $pallet[$i])->first();
                if (empty($gudangStok)) {
                    $gudangStok = new GudangStok;
                    $gudangStok->id_gudang      = $gudang->id;
                    $gudangStok->id_material    = $pallet[$i];
                    $gudangStok->status         = 1;
                    $gudangStok->jumlah         = $pallet_jumlah[$i];
                } else {
                    if ($action_pallet[$i] == 1) {
                        $gudangStok->jumlah         = $gudangStok->jumlah - $pallet_jumlah[$i];
                    } else if ($action_pallet[$i] == 2) {
                        $gudangStok->jumlah         = $gudangStok->jumlah + $pallet_jumlah[$i];
                    }
                }

                $gudangStok->save();

                $materialTrans = new MaterialTrans;
                $materialTrans->id_adjustment   = $materialAdjustment->id;
                $materialTrans->id_material     = $pallet[$i];
                $materialTrans->tipe            = $action_pallet[$i];
                $materialTrans->jumlah          = $pallet_jumlah[$i];
                $materialTrans->alasan          = $pallet_alasan[$i];
                $materialTrans->tanggal         = $materialAdjustment->tanggal;
                $materialTrans->status_pallet   = 1;
                $materialTrans->id_gudang_stok  = $gudangStok->id;
                $materialTrans->shift_id        = $shift_id;
                $materialTrans->save();
            }
        }

        $this->responseData = $materialAdjustment;
        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function uploadFile($id_gudang, Request $req)
    {
        $id = $req->get('id');
        $file = $req->file('file');

        $cek_penggunaan = MaterialAdjustment::find($id);

        if (!empty($cek_penggunaan)) {
            $ext = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();

            $filter = [
                'jpg',
                'png',
                'jpeg',
                'gif',
            ];

            if (in_array($ext, $filter)) {
                $path = storage_path('app/public') . '/material/' . $id;
                $req->file('file')->move($path, $filename);
                $resource = MaterialAdjustment::find($id);

                $resource->foto = $filename;
                $resource->save(); 

                return response()->json([
                    'code' => http_response_code(),
                    'msg' => 'success',
                    'data' => $filename
                ], http_response_code());
            } else {
                return response()->json([
                    'code' => http_response_code(),
                    'msg' => 'fail',
                ], http_response_code());
            }
        } else {
            return response()->json([
                'code' => http_response_code(),
                'msg' => 'fail',
            ], http_response_code());
        }
    }

    public function show($id_gudang, $id, MaterialAdjustment $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $gudang = Gudang::findOrFail($id_gudang);
            if (!empty($gudang)) {
                $res = $models::withoutGlobalScopes()->find($id);
    
                if (!empty($res)) {
                    $resProduk = DB::table('material_adjustment as ma')
                    ->select(
                        'ma.id',
                        'm.nama as nama',
                        'm.id as id_produk',
                        'a.nama as nama_area',
                        'ars.tanggal',
                        'mt.tipe',
                        'alasan',
                        'mt.jumlah',
                        'shift_id'
                    )
                    ->leftJoin('material_trans as mt', 'mt.id_adjustment', '=', 'ma.id')
                    ->leftJoin('material as m', 'mt.id_material', '=', 'm.id')
                    ->leftJoin('area_stok as ars', 'ars.id', '=', 'mt.id_area_stok')
                    ->leftJoin('area as a', 'a.id', '=', 'ars.id_area')
                    ->where('id_adjustment', $res->id)
                    ->where('kategori', 1)
                    ->get();

                    $resPallet = DB::table('material_adjustment as ma')
                    ->leftJoin('material_trans as mt', 'mt.id_adjustment', '=', 'ma.id')
                    ->leftJoin('material as m', 'mt.id_material', '=', 'm.id')
                    ->where('id_adjustment', $res->id)
                    ->where('kategori', 2)
                    ->get();

                    $this->responseCode = 200;
                    $this->responseMessage = 'Data tersedia.';
                    $this->responseData['material_adjustment'] = $res;
                    $this->responseData['produk'] = $resProduk;
                    $this->responseData['pallet'] = $resPallet;
                    $this->responseData['url'] = '{base_url}/watch/{pics_url}?token={access_token}&un={asset_id}&ctg=assets&src={pics_url}';
                } else {
                    $this->responseData = [];
                    $this->responseStatus = 'No Data Available';
                    $this->responseMessage = 'Data tidak tersedia';
                }
            } else {
                $this->responseCode = 400;
                $this->responseMessage = 'ID gudang tidak valid';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }
}
