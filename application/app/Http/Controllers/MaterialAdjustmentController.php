<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Models\MaterialAdjustment;
use App\Http\Models\MaterialTrans;
use App\Http\Requests\MaterialAdjusmentRequest;
use App\Http\Requests\MaterialRequest;
use Illuminate\Http\Request;

class MaterialAdjustmentController extends Controller
{
    public function index($id)
    {
        $data['title'] = 'Stock Adjustment';
        $data['id_gudang'] = $id;
        $gudang = Gudang::find($id);
        if (!empty($gudang)) {
            return view('stock-adjustment.grid', $data);
        } else {
            abort(404);
        }
    }

    public function json(Request $req)
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
            $result = $models->jsonGrid($start, $perpage, $search, false, $sort, $field, $condition);
            $total  = $models->jsonGrid($start, $perpage, $search, true, $sort, $field, $condition);
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
        $gudang = Gudang::find($id_gudang);
        if (!empty($gudang)) {
            $req->validate();

            $id = $req->input('id');
            if (!empty($id)) {
                $materialAdjustment = MaterialAdjustment::withoutGlobalScopes()->find($id);
            } else {
                $materialAdjustment = new MaterialAdjustment;
            }

            // dump($req->input('produk_jumlah'));

            //material adjustment
            $materialAdjustment->tanggal            = $req->input('tanggal');
            // $materialAdjustment->foto               = $req->input('foto');
            $materialAdjustment->save();

            //material trans
            $produk = $req->input('produk');
            $action_produk = $req->input('action_produk');
            $produk_jumlah = $req->input('produk_jumlah');
            if (!empty($pallet)) {
                $panjang          = count($produk);
                $produk           = array_values($produk);
                $action_produk    = array_values($action_produk);
                $produk_jumlah    = array_values($produk_jumlah);
                for ($i = 0; $i < $panjang; $i++) {
                    $materialTrans = new MaterialTrans;
                    $materialTrans->id_adjustment    = $materialAdjustment->id;
                    $materialTrans->id_material     = $produk[$i];
                    $materialTrans->tipe            = $action_produk[$i];
                    $materialTrans->jumlah          = $produk_jumlah[$i];
                    $materialTrans->save();
                }
            }

            $pallet = $req->input('pallet');
            $action_pallet = $req->input('action_pallet');
            $pallet_jumlah = $req->input('pallet_jumlah');
            if (!empty($pallet)) {
                $panjang          = count($pallet);
                $pallet           = array_values($pallet);
                $action_pallet    = array_values($action_pallet);
                $pallet_jumlah    = array_values($pallet_jumlah);
                for ($i = 0; $i < $panjang; $i++) {
                    $materialTrans = new MaterialTrans;
                    $materialTrans->id_adjustment    = $materialAdjustment->id;
                    $materialTrans->id_adjustment    = $materialAdjustment->id;
                    $materialTrans->id_material     = $pallet[$i];
                    $materialTrans->tipe            = $action_pallet[$i];
                    $materialTrans->jumlah          = $pallet_jumlah[$i];
                    $materialTrans->save();
                }
            }

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        } else {
            $this->responseCode = 400;
            $this->responseMessage = 'ID gudang tidak valid';
        }
        

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function uploadFile($id_material_adjustment)
    {
        
    }

    public function show($id_gudang, $id, MaterialAdjustment $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $gudang = Gudang::find($id_gudang);
            if (!empty($gudang)) {
                $res = $models::withoutGlobalScopes()->find($id);
    
                if (!empty($res)) {
                    $resTrans = MaterialTrans::where('id_adjustment', $res->id)->get();

                    $this->responseCode = 200;
                    $this->responseMessage = 'Data tersedia.';
                    $this->responseData['material_adjustment'] = $res;
                    $this->responseData['material_trans'] = $resTrans;
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
