<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Requests\AktivitasRequest;
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Aktivitas';
        return view('master.master-aktivitas.grid', $data);
    }

    public function create()
    {
        $data['title'] = 'Master Tambah Aktivitas';
        return view('master.master-aktivitas.second', $data);
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

    public function store(AktivitasRequest $req, Aktivitas $aktivitas)
    {
        $req->validated();

        $aktivitas->nama                       = $req->input('nama');
        $aktivitas->produk_stok                = $req->input('produk_stok');
        $aktivitas->produk_rusak               = $req->input('produk_rusak');
        $aktivitas->pallet_stok                = $req->input('pallet_stok');
        $aktivitas->pallet_dipakai             = $req->input('pallet_dipakai');
        $aktivitas->pallet_kosong              = $req->input('pallet_kosong');
        $aktivitas->pallet_rusak               = $req->input('pallet_rusak');
        $aktivitas->upload_foto                = $req->input('upload_foto');
        $aktivitas->connect_sistro             = $req->input('connect_sistro');
        $aktivitas->pengiriman                 = $req->input('pengiriman');
        $aktivitas->fifo                       = $req->input('fifo');
        $aktivitas->kelayakan                  = $req->input('kelayakan');
        $aktivitas->butuh_biaya                = $req->input('butuh_biaya');
        $aktivitas->peminjaman                 = $req->input('peminjaman');
        $aktivitas->pengaruh_tgl_produksi      = $req->input('pengaruh_tgl_produksi');
        $aktivitas->internal_gudang            = $req->input('internal_gudang');
        $aktivitas->butuh_alat_berat           = $req->input('butuh_alat_berat');
        $aktivitas->butuh_tkbm                 = $req->input('butuh_tkbm');
        $aktivitas->tanda_tangan               = $req->input('tanda_tangan');
        $aktivitas->butuh_approval             = $req->input('butuh_approval');
        $aktivitas->pindah_area                = $req->input('pindah_area');
        $aktivitas->start_date                 = $req->input('start_date');
        $aktivitas->end_date                   = $req->input('end_date');

        $aktivitas->save();

        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';

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
