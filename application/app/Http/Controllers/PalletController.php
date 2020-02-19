<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\ShiftKerja;
use App\Http\Requests\PalletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PalletController extends Controller
{
    public function index($id_gudang)
    {
        $gudang = Gudang::findOrFail($id_gudang);

        $data['stok'] = GudangStok::select(
            DB::raw('SUM(jumlah) as total')
        )
        ->where('id_gudang', $id_gudang)
        ->where('status', 1)
        ->first();

        $data['dipakai'] = GudangStok::select(
            DB::raw('SUM(jumlah) as total')
        )
        ->where('id_gudang', $id_gudang)
        ->where('status', 2)
        ->first();
        
        $data['kosong'] = GudangStok::select(
            DB::raw('SUM(jumlah) as total')
        )
        ->where('status', 3)
        ->where('id_gudang', $id_gudang)
        ->first();
        
        $data['rusak'] = GudangStok::select(
            DB::raw('SUM(jumlah) as total')
        )
        ->where('status', 4)
        ->where('id_gudang', $id_gudang)
        ->first();
        $data['nama_gudang'] = $gudang->nama;
        $data['id_gudang'] = $id_gudang;
        $data['shift'] = ShiftKerja::get();
        return view('list-pallet.grid', $data);
    }

    public function json(Request $req, $id_gudang)
    {
        $models = new GudangStok();

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
        $field = $columns[$numbcol[0]['column']]['data']??'tanggal';

        $condition = '';

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $result = $models->gridJson($start, $perpage, trim($search), false, $sort, $field, $condition, $id_gudang);
            $total  = $models->gridJson($start, $perpage, trim($search), true, $sort, $field, $condition, $id_gudang);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function getMaterial(Request $req)
    {
        $search = $req->input('q');
        $res = Material::pallet()->where('nama', 'ILIKE', '%' . strtolower($search) . '%')->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function store(PalletRequest $req, $id_gudang)
    {
        $req->validated();

        $gudangStok = GudangStok::where('id_material', $req->input('material'))->where('id_gudang', $id_gudang)->where('status', $req->input('jenis'))->first();
        if (empty($gudangStok)) {
            if ($req->input('tipe') == 1) {
                $this->responseMessage = 'Stok belum tersedia jadi Anda hanya diizinkan untuk menambah untuk material ini!';
                $this->responseCode = 403;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                return response()->json($response, $this->responseCode);
            }
            $gudangStok = new GudangStok();
            $gudangStok->jumlah = $req->input('jumlah');
        } else {
            if ($req->input('tipe') == 1) {
                if ($gudangStok->jumlah - $req->input('jumlah') < 0) {
                    $this->responseMessage = 'Jumlah yang Anda masukkan melebihi stok yang tersedia!';
                    $this->responseCode = 403;

                    $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                    return response()->json($response, $this->responseCode);
                }
                $gudangStok->jumlah         = $gudangStok->jumlah - $req->input('jumlah');
            } else if ($req->input('tipe') == 2) {
                $gudangStok->jumlah         = $gudangStok->jumlah + $req->input('jumlah');
            }
        }

        $gudangStok->id_gudang      = $id_gudang;
        $gudangStok->id_material    = $req->input('material');
        $gudangStok->status         = $req->jenis;
        $gudangStok->save();

        $arr = [
            'id_material'       => $req->input('material'),
            'id_gudang_stok'    => $gudangStok->id,
            'tanggal'           => $req->input('tanggal'),
            'tipe'              => $req->input('tipe'),
            'jumlah'            => $req->input('jumlah'),
            'alasan'            => $req->input('alasan'),
            'status_pallet'     => $req->jenis,
            'shift_id'          => $req->input('shift_id'),
        ];

        (new MaterialTrans)->create($arr);

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id_gudang, $id, GudangStok $models, Request $request)
    {
        $gudang = Gudang::find($id_gudang);
        if (!empty($gudang)) {
            $res = MaterialTrans::find($id);

            if (!empty($res)) {
                $resProduk = DB::table('gudang_stok as ma')
                    ->leftJoin('material_trans as mt', 'mt.id_gudang_stok', '=', 'ma.id')
                    ->leftJoin('material as m', 'mt.id_material', '=', 'm.id')
                    ->where('mt.id', $id)
                    ->where('kategori', 2)
                    ->first();

                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia.';
                $this->responseData = $resProduk;
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

    public function listPallet($id_gudang, $status)
    {
        $gudangStok = GudangStok::with('material')->where('id_gudang', $id_gudang)->where('status', $status)->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $gudangStok;
        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
