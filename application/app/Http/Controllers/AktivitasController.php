<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AktivitasController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index()
    {
        return view('master.master-aktivitas.grid');
    }

    public function create()
    {
        return view('master.master-aktivitas.second');
    }

    public function json(Request $req)
    {
        $models = new Aktivitas();

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

    public function store(Request $req)
    {
        $models = new Aktivitas;
        $rules = [
            'nama'              => 'required',
            'start_date'        => 'nullable|date_format:d-m-Y',
            'end_date'          => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $action = $req->input('action');
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $this->responseCode                 = 400;
            $this->responseStatus               = 'Missing Param';
            $this->responseMessage              = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log']    = $validator->errors();
        } else {
            $id                         = $req->input('id');
            $nama                       = strip_tags($req->input('nama'));
            $produk_stok                = $req->input('produk_stok');
            $pallet_stok                = $req->input('pallet_stok');
            $pallet_dipakai             = $req->input('pallet_dipakai');
            $pallet_kosong              = $req->input('pallet_kosong');
            $upload_foto                = $req->input('upload_foto');
            $connect_sistro             = $req->input('connect_sistro');
            $pengiriman                 = $req->input('pengiriman');
            $fifo                       = $req->input('fifo');
            $pengaruh_tgl_produksi      = $req->input('pengaruh_tgl_produksi');
            $internal_gudang            = $req->input('internal_gudang');
            $butuh_alat_berat           = $req->input('butuh_alat_berat');
            $butuh_tkbm                 = $req->input('butuh_tkbm');
            $tanda_tangan               = $req->input('tanda_tangan');

            $start_date  = null;
            if ($req->input('start_date') != '') {
                $start_date  = date('Y-m-d', strtotime($req->input('start_date')));
            }

            $end_date   = null;
            if ($req->input('end_date') != '') {
                $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
            }

            if (!empty($id)) {
                $models = Aktivitas::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $models->nama                       = $nama;
            $models->produk_stok                = $produk_stok;
            $models->pallet_stok                = $pallet_stok;
            $models->pallet_dipakai             = $pallet_dipakai;
            $models->pallet_kosong              = $pallet_kosong;
            $models->upload_foto                = $upload_foto;
            $models->connect_sistro             = $connect_sistro;
            $models->pengiriman                 = $pengiriman;
            $models->fifo                       = $fifo;
            $models->pengaruh_tgl_produksi      = $pengaruh_tgl_produksi;
            $models->internal_gudang            = $internal_gudang;
            $models->butuh_alat_berat           = $butuh_alat_berat;
            $models->butuh_tkbm                 = $butuh_tkbm;
            $models->tanda_tangan               = $tanda_tangan;
            $models->start_date                 = $start_date;
            $models->end_date                   = $end_date;

            $models->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, Aktivitas $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::find($id);

            if (!empty($res)) {
                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia.';
                $this->responseData = $res;
            } else {
                $this->responseData = [];
                $this->responseStatus = 'No Data Available';
                $this->responseMessage = 'Data tidak tersedia';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function edit($id)
    {
        $data['id'] = $id;
        return view('master.master-aktivitas.second', $data);
    }

    public function update(Request $request, Aktivitas $aktivitas)
    {
        //
    }

    public function destroy(Aktivitas $aktivitas)
    {
        //
    }
}
