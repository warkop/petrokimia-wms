<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Requests\PalletRequest;
use Illuminate\Http\Request;

class PalletController extends Controller
{
    public function index($id_gudang)
    {
        $gudang = Gudang::find($id_gudang);
        $data['nama_gudang'] = $gudang->nama;
        $data['id_gudang'] = $id_gudang;
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
            $result = $models->gridJson($start, $perpage, $search, false, $sort, $field, $condition, $id_gudang);
            $total  = $models->gridJson($start, $perpage, $search, true, $sort, $field, $condition, $id_gudang);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function getMaterial()
    {
        $res = Material::pallet()->get();

        $this->responseCode = 200;
        $this->responseMessage = 'Data tersedia.';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function store(PalletRequest $req, $id_gudang)
    {
        $req->validated();

        $gudangStok = GudangStok::where('id_material', $req->input('material'))->get();
        if (!empty($gudangStok)) {
            $gudangStok = new GudangStok;
        }
        // $arr_stok = [
        //     'id_gudang'         => $id_gudang,
        //     'id_material'       => $req->input('material'),
        //     'jumlah'            => $req->input('jumlah'),
        //     'status'            => $req->input('tipe'),
        // ];

        $gudangStok->id_gudang      = $id_gudang;
        $gudangStok->id_material    = $req->input('material');
        $gudangStok->jumlah         = $req->input('jumlah');
        $gudangStok->status         = $req->input('tipe');
        $gudangStok->save();

        $arr = [
            'id_material'       => $req->input('material'),
            'tanggal'           => $req->input('tanggal'),
            'tipe'              => $req->input('tipe'),
            'jumlah'            => $req->input('jumlah'),
            'alasan'            => $req->input('alasan'),
            'status_pallet'     => $req->input('jenis'),
        ];

        (new MaterialTrans)->create($arr);

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}