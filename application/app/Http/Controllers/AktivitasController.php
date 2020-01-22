<?php

namespace App\Http\Controllers;

use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasAlatBerat;
use App\Http\Models\AktivitasMasterFoto;
use App\Http\Models\JenisFoto;
use App\Http\Models\KategoriAlatBerat;
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
        $data['title']                  = 'Master Tambah Aktivitas';
        $data['foto']                   = JenisFoto::get();
        $data['alat_berat']             = KategoriAlatBerat::get();
        $data['anggaran_tkbm']          = '';
        $data['aktivitas_alat_berat']   = '';
        $data['aktivitas_master_foto']  = null;
        $data['aktivitas']              = '';
        $data['anggaran_pallet']        = '';
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

    public function store(AktivitasRequest $req, $id='')
    {
        $req->validated();

        if (!empty($id)) {
            $aktivitas = Aktivitas::withoutGlobalScopes()->find($id);
        } else {
            $aktivitas = new Aktivitas;
        }

        $aktivitas->nama                       = $req->input('nama');
        $aktivitas->produk_stok                = $req->input('produk_stok');
        $aktivitas->produk_rusak               = $req->input('produk_rusak');
        $aktivitas->pallet_stok                = $req->input('pallet_stok');
        $aktivitas->pallet_dipakai             = $req->input('pallet_dipakai');
        $aktivitas->pallet_kosong              = $req->input('pallet_kosong');
        $aktivitas->pallet_rusak               = $req->input('pallet_rusak');
        $aktivitas->connect_sistro             = $req->input('connect_sistro');
        $aktivitas->pengiriman                 = $req->input('pengiriman');
        $aktivitas->upload_foto                = $req->input('butuh_upload_foto');
        $aktivitas->butuh_alat_berat           = $req->input('butuh_alat_berat');
        $aktivitas->fifo                       = $req->input('fifo');
        $aktivitas->kelayakan                  = $req->input('kelayakan');
        $aktivitas->butuh_biaya                = $req->input('butuh_biaya');
        $aktivitas->peminjaman                 = $req->input('peminjaman');
        $aktivitas->pengaruh_tgl_produksi      = $req->input('pengaruh_tgl_produksi');
        $aktivitas->internal_gudang            = $req->input('internal_gudang');
        $aktivitas->butuh_tkbm                 = $req->input('butuh_tkbm');
        $aktivitas->tanda_tangan               = $req->input('tanda_tangan');
        $aktivitas->butuh_approval             = $req->input('butuh_approval');
        $aktivitas->so                         = $req->input('so');
        $aktivitas->tanpa_tanggal              = $req->input('tanpa_tanggal');
        $aktivitas->penyusutan                 = $req->input('penyusutan');
        $aktivitas->kode_aktivitas             = $req->input('kode_aktivitas');
        $aktivitas->penerimaan_gi              = $req->input('penerimaan_gi');
        $aktivitas->biaya_pallet               = $req->input('biaya_pallet');
        $aktivitas->start_date                 = $req->input('start_date');
        $aktivitas->end_date                   = $req->input('end_date');

        $anggaran_tkbm = ($req->input('anggaran_tkbm') ? $req->input('anggaran_tkbm') : 0);
        $anggaran_tkbm = str_replace('.', '', $anggaran_tkbm);
        $temp_tkbm = explode(',', $anggaran_tkbm);
        $aktivitas->anggaran_tkbm = $temp_tkbm[0];

        $anggaran_pallet = ($req->input('anggaran_pallet') ? $req->input('anggaran_pallet') : 0);
        $anggaran_pallet = str_replace('.', '', $anggaran_pallet);
        $temp_pallet = explode(',', $anggaran_pallet);
        $aktivitas->anggaran_pallet = $temp_pallet[0];

        $aktivitas->save();

        $butuh_upload_foto      = $req->input('butuh_upload_foto');
        $upload_foto            = $req->input('upload_foto');
        $butuh_alat_berat       = $req->input('butuh_alat_berat');
        $alat_berat             = $req->input('alat_berat');
        $anggaran               = $req->input('anggaran');
        
        if (!empty($upload_foto) && !empty($butuh_upload_foto)) {
            AktivitasMasterFoto::where('id_aktivitas', $aktivitas->id)->delete();
            for ($i=0; $i<count($upload_foto); $i++) {
                $arr = [
                    'id_aktivitas'  => $aktivitas->id,
                    'id_foto_jenis' => $upload_foto[$i],
                ];

                AktivitasMasterFoto::create($arr);
            }
        }
        
        if (!empty($alat_berat) && !empty($butuh_alat_berat)) {
            AktivitasAlatBerat::where('id_aktivitas', $aktivitas->id)->delete();
            for ($i=0; $i<count($alat_berat); $i++) {
                $anggaran[$alat_berat[$i]] = ($anggaran[$alat_berat[$i]] ? $anggaran[$alat_berat[$i]] : 0);
                $anggaran[$alat_berat[$i]] = str_replace('.', '', $anggaran[$alat_berat[$i]]);
                $temps = explode(',', $anggaran[$alat_berat[$i]]);
                $temp_anggaran = $temps[0];

                $arr = [
                    'id_aktivitas'              => $aktivitas->id,
                    'id_kategori_alat_berat'    => $alat_berat[$i],
                    'anggaran'                  => $temp_anggaran,
                ];
    
                AktivitasAlatBerat::create($arr);
            }
        }


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
            $res = $models::withoutGlobalScopes()->findOrFail($id);

            if (!empty($res)) {
                $this->responseCode     = 200;
                $this->responseMessage  = 'Data tersedia.';
                $this->responseData     = $res;
            } else {
                $this->responseData     = [];
                $this->responseStatus   = 'No Data Available';
                $this->responseMessage  = 'Data tidak tersedia';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function edit($id)
    {
        $aktivitas                      = Aktivitas::withoutGlobalScopes()->findOrFail($id);
        $data['id']                     = $id;
        $data['anggaran_tkbm']          = $aktivitas->anggaran_tkbm;
        $data['foto']                   = JenisFoto::get();
        $data['alat_berat']             = KategoriAlatBerat::get();
        $data['aktivitas_alat_berat']   = AktivitasAlatBerat::where('id_aktivitas', $id)->get();
        $data['aktivitas_master_foto']  = AktivitasMasterFoto::where('id_aktivitas', $id)->get();
        $data['aktivitas']              = $aktivitas;
        $data['anggaran_pallet']        = $aktivitas->anggaran_pallet;
        return view('master.master-aktivitas.second', $data);
    }

    public function getFotoOfAktivitas($id)
    {
        Aktivitas::withoutGlobalScopes()->findOrFail($id);

        $res = AktivitasMasterFoto::where('id_aktivitas', $id)->get();

        return response()->json(['data' => $res], 200);
    }

    public function getAlatBeratOfAktivitas($id)
    {
        Aktivitas::withoutGlobalScopes()->findOrFail($id);

        $res = AktivitasAlatBerat::where('id_aktivitas', $id)->get();

        return response()->json(['data' => $res], 200);
    }
}
