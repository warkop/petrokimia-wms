<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\Users;
use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasGudang;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasHarianAlatBerat;
use App\Http\Models\AktivitasHarianArea;
use App\Http\Models\AktivitasKelayakanFoto;
use App\Http\Models\AktivitasMasterFoto;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Karu;
use App\Http\Models\KategoriAlatBerat;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RencanaAlatBerat;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\Sistro;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Models\Yayasan;
use App\Http\Requests\ApiAktivitasPenerimaanGiRequest;
use App\Http\Requests\ApiAktivitasPengembalianRequest;
use App\Http\Requests\ApiAktivitasRequest;
use App\Http\Requests\ApiSaveKelayakanPhotos;
use App\Http\Requests\ApiSavePhotosRequest;
use App\Http\Resources\AktivitasResource;
use App\Http\Resources\AlatBeratResource;
use App\Http\Resources\AreaPenerimaanGiResource;
use App\Http\Resources\GetSistroResource;
use App\Http\Resources\HistoryMaterialAreaResource;
use App\Http\Resources\ListNotifikasiResource;
use App\Notifications\Pengiriman;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AktivitasController extends Controller
{
    private function getCheckerGudang() { //untuk memperoleh informasi checker ini sekarang berada di gudang mana
        if (request()->get('my_auth')->role == 3) {
            $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
                ->where('id_tkbm',request()->get('my_auth')->id_tkbm)
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

    private function storeNotification($aktivitasHarian, $message='', $penerimaan=false) //save notifikasi
    {
        if ($penerimaan) {
            $gudang = Gudang::findOrFail($aktivitasHarian->id_gudang);
        } else {
            $gudang = Gudang::findOrFail($aktivitasHarian->id_gudang_tujuan);
        }
        $gudang->notify(new Pengiriman($aktivitasHarian));

        $aktivitas = Aktivitas::find($aktivitasHarian->id_aktivitas);
        $user = Users::find(request()->get('my_auth')->id_user);

        $rencanaHarian = RencanaHarian::withoutGlobalScopes()->where('id_gudang', $aktivitasHarian->id_gudang_tujuan)
            ->orderBy('id', 'desc')
            ->first();

        if ($rencanaHarian) {
            $rencanaTkbm = RencanaTkbm::where('id_rencana', $rencanaHarian->id)
                ->join('rencana_harian', 'rencana_tkbm.id_rencana', '=', 'rencana_harian.id')
                ->where(function ($query) {
                    $query->where('end_date');
                    $query->orWhere('end_date', '>=', now());
                })
                ->get();

            foreach ($rencanaTkbm as $key) {
                $user = Users::where('id_tkbm', $key->id_tkbm)->first();
                if (!empty($user)) {
                    send_firebase(
                        $user->user_gcid,
                        [
                            'title' => $aktivitas->nama,
                            'message' => $message,
                            'meta' => [
                                'id' => $aktivitasHarian->id,
                                'id_aktivitas'      => $aktivitasHarian->id_aktivitas,
                                'kode_aktivitas'    => $aktivitasHarian->kode_aktivitas,
                                'approve'           => $aktivitasHarian->approve,
                                'pengiriman_gi'     => $aktivitasHarian->aktivitas->pengiriman_gi,
                                'penerimaan_gi'     => $aktivitasHarian->aktivitas->penerimaan_gi,
                            ],
                        ]
                    );
                }
            }
        }
    }

    public function index(Request $req) //memuat daftar aktivitas
    {
        $gudang = $this->getCheckerGudang();
        $search = strip_tags($req->input('search'));

        $my_auth = request()->get('my_auth');
        $user = Users::findOrFail($my_auth->id_user);
        $res = AktivitasGudang::join('aktivitas', 'aktivitas.id', '=', 'aktivitas_gudang.id_aktivitas')
            ->where('id_gudang', $gudang->id)
            ->whereNull('penerimaan_gi')
            ->where(function($query){
                $query->where('end_date', null);
                $query->orWhere('end_date', '>=', now());
            })
            ->where(function ($where) use ($search) {
                $where->where(DB::raw('nama'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->orderBy('id', 'desc');

        if ($user->role_id == 5) {
            $res = $res->whereNotNull('aktivitas.peminjaman');
        } else {
            $res = $res->whereNull('peminjaman');
        }

        $obj =  AktivitasResource::collection($res->paginate(10))->additional([
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function getMaterial(Request $req) //memuat produk
    {
        $search = strip_tags($req->input('search'));
        $resource = Material::produk()->where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }
    
    public function getPallet(Request $req) //memuat pallet
    {
        $search = strip_tags($req->input('search'));
        $resource = Material::pallet()->where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getGudang(Request $req) //memuat gudang tujuan
    {
        $search = strip_tags($req->input('search'));
        $resource = Gudang::where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getTkbm(Request $req) //memuat tkbm
    {
        $search = strip_tags($req->input('search'));

        $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
            ->where('id_tkbm', request()->get('my_auth')->id_tkbm)
            ->orderBy('rencana_harian.id', 'desc')
            ->first();

        if (empty($rencana_tkbm)) {
            $this->responseCode = 500;
            $this->responseMessage = 'Checker tidak terdaftar pada rencana harian apapun!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        $rencana_harian = RencanaHarian::findOrFail($rencana_tkbm->id_rencana);

        $resource = RencanaTkbm::select(
            'tenaga_kerja_non_organik.id',
            'tenaga_kerja_non_organik.nama'
        )
        ->join('tenaga_kerja_non_organik', 'tenaga_kerja_non_organik.id', '=', 'rencana_tkbm.id_tkbm')
        ->where('job_desk_id', 2)
        ->where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(tenaga_kerja_non_organik.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })
        ->where('id_rencana', $rencana_harian->id)
        ->where(function($query) {
            $query->where('tenaga_kerja_non_organik.end_date', null)
            ->orWhere('tenaga_kerja_non_organik.end_date', '>=', now());
        })
        ->get();

        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getArea(Request $req, $id_aktivitas, $id_material, $pindah=false) //memuat area
    {
        $user = $req->get('my_auth');
        $gudang = $this->getCheckerGudang();

        $search = strip_tags($req->input('search'));
        $aktivitas = Aktivitas::findOrFail($id_aktivitas);
        $condition = '';
        if ($aktivitas->produk_rusak != 2) {
            $condition = ' and area_stok.status = 1';
        } else if ($aktivitas->produk_rusak != 1) {
            $condition = ' and area_stok.status = 2';
        } else {
            if ($aktivitas->produk_stok != null) {
                $condition = ' and area_stok.status = 1';
            }
        }
        
        if ($aktivitas->pengaruh_tgl_produksi != null) {
            $resource = Area::
            select(
                'area.id',
                'area.nama',
                'area.kapasitas',
                'area_stok.tanggal',
                'area.tipe',
                'area_stok.id_material',
                DB::raw('COALESCE((SELECT sum(jumlah) FROM area_stok where id_area = area.id and id_material = '.$id_material.$condition.'),0) as jumlah'),
                'area_stok.jumlah as jumlah_area'
            )
            ->leftJoin('area_stok', 'area_stok.id_area', '=', 'area.id')
            ->where('id_gudang', $gudang->id)
            ->where('area_stok.status', 1)
            ;

            if ($pindah == false) {
                $resource = $resource->where('id_material', $id_material);
            }
            $resource = $resource->get();
        } else {
            $tanggal = date('Y-m-d', strtotime(now()));
            $resource = Area::select(
                'area.id',
                'area.nama',
                'area.kapasitas',
                DB::raw("TO_CHAR(now(),'YYYY-MM-DD') as tanggal"),
                DB::raw("COALESCE((SELECT sum(jumlah) FROM area_stok where id_area = area.id and id_material = " . $id_material . " and tanggal = '".$tanggal."') ,0) as jumlah")
            )
            ->leftJoin('area_stok', 'area_stok.id_area', '=', 'area.id')
            ->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->where('id_gudang', $gudang->id)
            ->get();
        }
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function pindahArea(Request $req) //memuat area untuk keperluan pindah area
    {
        $search = strip_tags($req->input('search'));
        $resource = Area::where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getAreaStok($id_aktivitas, $id_material, $id_area) //memuat list tanggal apa saja yang tersedia pada gudang ini
    {
        $aktivitas = Aktivitas::findOrFail($id_aktivitas);
        if ($aktivitas->pengaruh_tgl_produksi != null) {
            $detail = DB::table('')->selectRaw(
                '
                        area_stok.id,
                        area.nama,
                        area.kapasitas,
                        area_stok.tanggal,
                        area_stok.jumlah'
            )
                ->from('area_stok')
                ->join('area', 'area.id', '=', 'area_stok.id_area')
                ->where('id_material', $id_material)
                ->where('area_stok.id_area', $id_area);
            if ($aktivitas->fifo != null) {
                $detail = $detail->orderBy('tanggal', 'ASC');
            } else {
                $detail = $detail->orderBy('nama', 'ASC');
            }

            return (new AktivitasResource($detail->get()))->additional([
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK,
                ]
            ], Response::HTTP_OK);
        } else {
            $detail = [];

            return [
                'data' => $detail,
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK,
                ]
            ];
        }
    }

    public function getAlatBerat(Request $req, $id_aktivitas) //memuat alat berat
    {
        $search = strip_tags($req->input('search'));

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

        $resource = RencanaAlatBerat::distinct()->select(
            'alat_berat.id',
            'alat_berat_kat.nama as kategori',
            'nomor_lambung'
        )
        ->join('alat_berat', 'alat_berat.id', '=', 'rencana_alat_berat.id_alat_berat')
        ->join('alat_berat_kat', 'alat_berat_kat.id', '=', 'alat_berat.id_kategori')
        ->join('aktivitas_alat_berat', 'alat_berat_kat.id', '=', 'aktivitas_alat_berat.id_kategori_alat_berat')
        ->where('id_aktivitas', $id_aktivitas)
        ->where('id_rencana', $rencana_harian->id)
        ->where(function($query){
            $query->where('alat_berat_kat.end_date', null);
            $query->orWhere('alat_berat_kat.end_date', '>=', now());
        })
        ->orderBy('alat_berat.id', 'asc')
        ->get()
        ;

        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getPenerimaan(AktivitasHarian $aktivitasHarian)
    {
        $data = AktivitasHarianArea::where('id_aktivitas_harian', $aktivitasHarian->id)->with('area_stok')->get();
        return (new AktivitasResource($data))->additional([
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);
    }

    public function checkProduk($id_area, $id_material, $tanggal)
    {
        $areaStok = AreaStok::where('id_area', $id_area)
        ->where('id_material', $id_material)
        ->where('tanggal', date('Y-m-d', strtotime($tanggal)))
        ->first();
        if (!empty($areaStok)) {
            
        }
    }

    public function store(ApiAktivitasRequest $req, $draft=0, $id='') //menyimpan aktivitas harian secara reguler
    {
        $req->validated();

        $user       = $req->get('my_auth');
        $res_user   = Users::findOrFail($user->id_user);
        $gudang     = $this->getCheckerGudang();

        if (!empty($id)) {
            $aktivitasHarian = AktivitasHarian::find($id);
            if (!empty($aktivitasHarian) && $aktivitasHarian->draft == 0) {
                $this->responseCode = 403;
                $this->responseMessage = 'Aktivitas tidak dalam status draft, Anda tidak bisa mengubahnya.';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
            MaterialTrans::where('id_aktivitas_harian', $aktivitasHarian->id)->forceDelete();
            AktivitasHarianArea::where('id_aktivitas_harian',  $aktivitasHarian->id)->forceDelete();

            $aktivitasHarian->updated_by = $res_user->id;
        } else {
            $aktivitasHarian = new AktivitasHarian;
            $aktivitasHarian->created_by        = $res_user->id;
            $aktivitasHarian->updated_by        = $res_user->id;
        }

        //simpan aktivitas
        if ($res_user->role_id == 3) {
            $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
                ->where('id_tkbm', $user->id_tkbm)
                ->orderBy('rencana_harian.id', 'desc')
                ->first();

            if (empty($rencana_tkbm)) {
                $this->responseCode     = 500;
                $this->responseMessage  = 'Checker tidak terdaftar pada rencana harian apapun!';
                $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
            $aktivitasHarian->id_shift          = $rencana_tkbm->id_shift;
        }

        $aktivitasHarian->id_aktivitas      = $req->input('id_aktivitas');
        $aktivitasHarian->id_gudang         = $gudang->id;
        // $aktivitasHarian->id_karu           = $gudang->id_karu;
        $aktivitasHarian->id_gudang_tujuan  = $req->input('id_gudang_tujuan');
        $aktivitasHarian->ref_number        = $req->input('ref_number');
        // $aktivitasHarian->id_alat_berat     = $req->input('id_alat_berat');
        $aktivitasHarian->sistro            = $req->input('sistro');
        $aktivitasHarian->approve           = $req->input('approve');
        $aktivitasHarian->kelayakan_before  = $req->input('kelayakan_before');
        $aktivitasHarian->kelayakan_after   = $req->input('kelayakan_after');
        $aktivitasHarian->dikembalikan      = $req->input('dikembalikan');
        $aktivitasHarian->alasan            = $req->input('alasan');
        $aktivitasHarian->so                = $req->input('so');
        $aktivitasHarian->id_yayasan        = $req->input('id_yayasan');
        $aktivitasHarian->id_tkbm           = $req->input('id_tkbm');
        $aktivitasHarian->draft             = $draft;

        $aktivitasHarian->save();

        $res_aktivitas = Aktivitas::find($req->input('id_aktivitas'));

        //simpan produk
        if ($res_aktivitas->pengaruh_tgl_produksi != null) { //jika tidak pengaruh tanggal produksi dicentang
            $list_produk = $req->input('list_produk');

            if (!empty($list_produk)) {
                $jums_list_produk = count($list_produk);

                for ($i = 0; $i < $jums_list_produk; $i++) {
                    $produk         = $list_produk[$i]['produk'];
                    $status_produk  = $list_produk[$i]['status_produk'];
                    $list_area      = $list_produk[$i]['list_area'];
                    $jums_list_area = count($list_area);

                    for ($j = 0; $j < $jums_list_area; $j++) {
                        $tipe               = $list_area[$j]['tipe'];
                        $id_area            = $list_area[$j]['id_area_stok'];
                        $list_jumlah        = $list_area[$j]['list_jumlah'];
                        $jums_list_jumlah   = count($list_jumlah);

                        for ($k = 0; $k < $jums_list_jumlah; $k++) {
                            if ($res_aktivitas->fifo != null) { //jika FIFO
                                $area_stok = AreaStok::where('id_area', $id_area)
                                ->where('id_material', $produk)
                                ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                ->where('status', $status_produk)
                                ->orderBy('tanggal', 'asc')
                                ->first();
                            } else {
                                $area_stok = AreaStok::where('id_area', $id_area)
                                ->where('id_material', $produk)
                                ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                ->where('status', $status_produk)
                                ->first();
                            }

                            if (!empty($area_stok)) {
                                if ($tipe == 1) {
                                    $area_stok->jumlah = $area_stok->jumlah - $list_jumlah[$k]['jumlah'];
                                } else {
                                    $area_stok->jumlah = $area_stok->jumlah + $list_jumlah[$k]['jumlah'];
                                }

                                $area_stok->status      = $status_produk;
                            } else {
                                $area_stok              = new AreaStok;
                                $area_stok->id_area     = $id_area;
                                $area_stok->id_material = $produk;
                                $area_stok->tanggal     = date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']));
                                $area_stok->status      = $status_produk;
                                $area_stok->jumlah      = $list_jumlah[$k]['jumlah'];
                            }

                            if ($aktivitasHarian->draft == 0) {
                                $area_stok->save();
                            }


                            $material_trans = new MaterialTrans;
                            $array = [
                                'id_material'           => $produk,
                                'id_aktivitas_harian'   => $aktivitasHarian->id,
                                'tanggal'               => date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])),
                                'tipe'                  => $tipe,
                                'jumlah'                => $list_jumlah[$k]['jumlah'],
                                'status_produk'         => $status_produk,
                                'id_area_stok'          => $area_stok->id,
                            ];
                            $material_trans->create($array);

                            (new AktivitasHarianArea)->create([
                                'id_aktivitas_harian'   => $aktivitasHarian->id,
                                'id_area_stok'          => $area_stok->id,
                                'jumlah'                => $list_jumlah[$k]['jumlah'],
                                'tipe'                  => $tipe,
                                'created_at'            => date('Y-m-d H:i:s'),
                                'created_by'            => $res_user->id,
                            ]);
                        }
                    }
                }
            }
        } else { //jika tidak pengaruh tanggal produksi tidak dicentang
            $list_produk = $req->input('list_produk');

            if (!empty($list_produk)) {
                $jums_list_produk = count($list_produk);

                for ($i = 0; $i < $jums_list_produk; $i++) {
                    $produk         = $list_produk[$i]['produk'];
                    $status_produk  = $list_produk[$i]['status_produk'];
                    
                    $list_area = $list_produk[$i]['list_area'];
                    if (!empty($list_area)) {
                        $jums_list_area = count($list_area);
                        for ($j = 0; $j < $jums_list_area; $j++) {
                            $tipe               = $list_area[$j]['tipe'];
                            $id_area            = $list_area[$j]['id_area_stok'];
                            $list_jumlah        = $list_area[$j]['list_jumlah'];
                            $jums_list_jumlah   = count($list_jumlah);

                            for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                $area_stok = AreaStok::where('id_area', $id_area)
                                    ->where('id_material', $produk)
                                    ->where('tanggal', date('Y-m-d'))
                                    ->where('status', $status_produk)
                                    ->first();

                                if (empty($area_stok)) {
                                    $area_stok = new AreaStok();
                                }

                                if ($tipe == 1) {
                                    $area_stok->jumlah = $area_stok->jumlah - $list_jumlah[$k]['jumlah'];
                                } else {
                                    $area_stok->jumlah = $area_stok->jumlah + $list_jumlah[$k]['jumlah'];
                                }

                                $area_stok->id_material   = $produk;
                                $area_stok->id_area       = $id_area;
                                $area_stok->tanggal       = date('Y-m-d');
                                $area_stok->status        = $status_produk;

                                if ($aktivitasHarian->draft == 0) {
                                    $area_stok->save();
                                }

                                $material_trans = new MaterialTrans;

                                $array = [
                                    'id_material'           => $produk,
                                    'id_aktivitas_harian'   => $aktivitasHarian->id,
                                    'tanggal'               => date('Y-m-d H:i:s'),
                                    'tipe'                  => $tipe,
                                    'jumlah'                => $list_jumlah[$k]['jumlah'],
                                    'status_produk'         => $status_produk,
                                    'id_area_stok'          => $area_stok->id,
                                ];
                                $material_trans->create($array);
                                
                                (new AktivitasHarianArea)->create([
                                    'id_aktivitas_harian'   => $aktivitasHarian->id,
                                    'id_area_stok'          => $area_stok->id,
                                    'jumlah'                => $list_jumlah[$k]['jumlah'],
                                    'tipe'                  => $tipe,
                                    'created_at'            => date('Y-m-d H:i:s'),
                                    'created_by'            => $res_user->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        //simpan pallet (stok=1, dipakai=2, kosong=3, rusak=4)
        $list_pallet = $req->input('list_pallet');
        if (!empty($list_pallet)) {
            $jums_list_pallet = count($list_pallet);

            for ($i = 0; $i < $jums_list_pallet; $i++) {
                $pallet         = $list_pallet[$i]['pallet'];
                $jumlah         = $list_pallet[$i]['jumlah'];
                $status_pallet  = $list_pallet[$i]['status_pallet'];
                $tipe           = $list_pallet[$i]['tipe'];

                $gudangStok = GudangStok::where('id_gudang', $gudang->id)->where('status', $status_pallet)->where('id_material', $pallet)->first();

                if (empty($gudangStok)) {
                    $gudangStok = new GudangStok;
                    $gudangStok->jumlah = $jumlah;
                } else {
                    if ($tipe == 1) {
                        $gudangStok->jumlah = $gudangStok->jumlah - $jumlah;
                    } else {
                        $gudangStok->jumlah = $gudangStok->jumlah + $jumlah;
                    }
                }

                $gudangStok->id_gudang     = $gudang->id;
                $gudangStok->id_material   = $pallet;
                $gudangStok->status        = $status_pallet;

                if ($aktivitasHarian->draft == 0) {
                    $gudangStok->save();
                }

                $arr = [
                    'id_aktivitas_harian'       => $aktivitasHarian->id,
                    'tanggal'                   => date('Y-m-d H:i:s'),
                    'id_material'               => $pallet,
                    'jumlah'                    => $jumlah,
                    'tipe'                      => $tipe,
                    'status_pallet'             => $status_pallet,
                ];

                $gudangStok->materialTrans()->create($arr);
            }
        }

        $list_alat_berat = $req->input('list_alat_berat');
        if (!empty($list_alat_berat)) {
            $jums_list_alat_berat = count($list_alat_berat);
            $toBeSave = [];
            for ($i = 0; $i < $jums_list_alat_berat; $i++) {
                $id_alat_berat  = $list_alat_berat[$i]['id_alat_berat'];
                $toBeSave[$i] = $id_alat_berat;
            }
            $aktivitasHarian->aktivitasHarianAlatBerat()->sync($toBeSave);
        }

        if ($res_aktivitas->internal_gudang != null) {
            $message = 'Ada pengiriman dari ' . $aktivitasHarian->gudang->nama . ' dengan nama ' . $res_aktivitas->nama;
            $this->storeNotification($aktivitasHarian, $message);
        }

        return (new AktivitasResource($aktivitasHarian))->additional([
            'produk' => $list_produk,
            'pallet' => $list_pallet,
            'status' => [
                'message'   => '',
                'code'      => Response::HTTP_CREATED,
            ]
        ], Response::HTTP_CREATED);
    }

    public function storePhotos(ApiSavePhotosRequest $req, AktivitasHarian $aktivitas) //menyimpan ttd dan foto jenis
    {
        $req->validated();

        $user = $req->get('my_auth');

        $res_user = Users::findOrFail($user->id_user);

        $id_aktivitas_harian = $req->input('id_aktivitas_harian');
        $aktivitas = AktivitasHarian::findOrFail($id_aktivitas_harian);


        $ttd = $req->file('ttd');
        if (!empty($ttd)) {
            if ($ttd->isValid()) {
                $ttd->storeAs('/public/aktivitas_harian/' . $id_aktivitas_harian, $ttd->getClientOriginalName());
                $aktivitas->ttd = $ttd->getClientOriginalName();
                $aktivitas->save();
            }
        }

        //simpan foto
        $foto = $req->file('foto');
        $foto_jenis = $req->input('id_foto_jenis');
        $lat = $req->input('lat');
        $lng = $req->input('lng');
        if (!empty($foto)) {
            $panjang = count($foto);
            (new AktivitasFoto)->where('id_aktivitas_harian', '=', $id_aktivitas_harian)->delete();
            for ($i = 0; $i < $panjang; $i++) {
                if ($foto[$i]->isValid()) {
                    $aktivitasFoto = new AktivitasFoto;

                    $foto[$i]->storeAs('/public/aktivitas_harian/' . $id_aktivitas_harian, $foto[$i]->getClientOriginalName());

                    $arrayFoto = [
                        'id_aktivitas_harian'       => $id_aktivitas_harian,
                        'id_foto_jenis'             => $foto_jenis[$i],
                        'foto'                      => $foto[$i]->getClientOriginalName(),
                        'size'                      => $foto[$i]->getSize(),
                        'lat'                       => $lat[$i],
                        'lng'                       => $lng[$i],
                        'created_by'                => $res_user->id,
                        'created_at'                => date('Y-m-d H:i:s'),
                    ];

                    $aktivitasFoto->create($arrayFoto);
                }
            }

            $foto = AktivitasFoto::where('id_aktivitas_harian', $id_aktivitas_harian)->get();
        }

        return response()->json([
            'data' => $foto,
            'status' => [
                'message' => 'Berhasil disimpan',
                'code' => Response::HTTP_CREATED,
            ]], 200);
    }

    public function storeKelayakanPhotos(ApiSaveKelayakanPhotos $req) //menyimpan foto kelayakan
    {
        $req->validated();

        $user = $req->get('my_auth');

        $res_user = Users::findOrFail($user->id_user);

        $id_aktivitas_harian = $req->input('id_aktivitas_harian');
        $aktivitas = AktivitasHarian::findOrFail($id_aktivitas_harian);
        $res_aktivitas = Aktivitas::findOrFail($aktivitas->id_aktivitas);
        if ($res_aktivitas->kelayakan != null) {
            //simpan foto
            $foto = $req->file('foto');
            $jenis = $req->input('jenis');
            if (!empty($foto)) {
                $panjang = count($foto);
                (new AktivitasKelayakanFoto)->where('id_aktivitas_harian', '=', $id_aktivitas_harian)->delete();
                Storage::deleteDirectory('/public/kelayakan/' . $id_aktivitas_harian);
                for ($i = 0; $i < $panjang; $i++) {
                    if ($foto[$i]->isValid()) {
                        $aktivitasKelayakanFoto = new AktivitasKelayakanFoto;

                        storage_path('app/public/kelayakan/') . $id_aktivitas_harian;
                        $md5Name = md5_file($foto[$i]->getRealPath());
                        $guessExtension = $foto[$i]->getClientOriginalExtension();
                        $foto[$i]->storeAs('/public/kelayakan/' . $id_aktivitas_harian, $md5Name . '.' . $guessExtension);

                        $arrayFoto = [
                            'id_aktivitas_harian'       => $id_aktivitas_harian,
                            'jenis'                     => $jenis[$i],
                            'foto'                      => $foto[$i]->getClientOriginalName(),
                            'size'                      => $foto[$i]->getSize(),
                            'ekstensi'                  => $foto[$i]->getClientOriginalExtension(),
                            'file_enc'                  => $md5Name . '.' . $guessExtension,
                            'created_by'                => $res_user->id,
                            'created_at'                => date('Y-m-d H:i:s'),
                        ];

                        $aktivitasKelayakanFoto->create($arrayFoto);
                    }
                }

                $foto = AktivitasKelayakanFoto::where('id_aktivitas_harian', $id_aktivitas_harian)->get();
            }

            return response()->json([
                'data' => $foto,
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_CREATED,
                ]
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'data' => null,
                'status' => [
                    'message' => 'Opsi kelayakan tidak tersedia pada aktivitas ini!',
                    'code' => Response::HTTP_FORBIDDEN
                ]
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function storePenerimaan(ApiAktivitasPenerimaanGiRequest $req) //menyimpan aktivitas harian untuk keperluan penerimaan GI
    {
        $req->validated();
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $aktivitasHarian = AktivitasHarian::findOrFail($req->input('id_aktivitas_harian'));

        $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
            ->where('id_tkbm', $user->id_tkbm)
            ->orderBy('rencana_harian.id', 'desc')
            ->take(1)->first();

        if (empty($rencana_tkbm)) {
            $this->responseCode = 500;
            $this->responseMessage = 'Checker tidak terdaftar pada rencana harian apapun!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        $gudang = Gudang::findOrFail($aktivitasHarian->id_gudang_tujuan);

        if (empty($gudang)) {
            $this->responseCode = 500;
            $this->responseMessage = 'Gudang tidak tersedia!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        $aktivitasGudang = AktivitasGudang::with('aktivitas')->whereHas('aktivitas', function ($query) {
            $query->whereNotNull('penerimaan_gi');
        })
        ->where('id_gudang', $gudang->id)
        ->first();
        
        if ($aktivitasHarian->approve != null) {
            $this->responseCode = 403;
            $this->responseMessage = 'Aktivitas harian sudah disetujui!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        if (!empty($aktivitasGudang)) {
            if ($aktivitasGudang->aktivitas->penerimaan_gi != null) {
                $wannaSave = new AktivitasHarian;
                $wannaSave->ref_number        = $aktivitasHarian->id;
                $wannaSave->id_aktivitas      = $aktivitasGudang->id_aktivitas;
                $wannaSave->id_gudang         = $gudang->id;
                // $wannaSave->id_karu           = $gudang->id_karu;
                $wannaSave->id_shift          = $rencana_tkbm->id_shift;
                $wannaSave->id_area           = $req->input('id_pindah_area');
                $wannaSave->id_alat_berat     = $req->input('id_alat_berat');
                $wannaSave->sistro            = $req->input('sistro');
                $wannaSave->alasan            = $req->input('alasan');
                $wannaSave->created_by        = $res_user->id;
                $wannaSave->created_at        = date('Y-m-d H:i:s');
    
                $wannaSave->save();
    
                $aktivitasHarian->approve = date('Y-m-d H:i:s');
                $aktivitasHarian->save();
    
                if ($aktivitasGudang->aktivitas->pengaruh_tgl_produksi != null) { //jika tidak pengaruh tanggal produksi dicentang
                    $list_produk = $req->input('list_produk');
    
                    if (!empty($list_produk)) {
                        $jums_list_produk = count($list_produk);
    
                        for ($i = 0; $i < $jums_list_produk; $i++) {
                            $produk = $list_produk[$i]['produk'];
                            $status_produk = $list_produk[$i]['status_produk'];
                            $list_area = $list_produk[$i]['list_area'];
                            $jums_list_area = count($list_area);
    
                            for ($j = 0; $j < $jums_list_area; $j++) {
                                $tipe = $list_area[$j]['tipe'];
                                $id_area = $list_area[$j]['id_area_stok'];
                                $list_jumlah = $list_area[$j]['list_jumlah'];
                                $jums_list_jumlah = count($list_jumlah);
    
                                for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                    if ($aktivitasGudang->aktivitas->fifo != null) { //jika FIFO
                                        $area_stok = AreaStok::where('id_area', $id_area)
                                            ->where('id_material', $produk)
                                            ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                            ->orderBy('tanggal', 'asc')
                                            ->first();
                                    } else {
                                        $area_stok = AreaStok::where('id_area', $id_area)
                                            ->where('id_material', $produk)
                                            ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                            ->first();
                                    }
    
                                    if (!empty($area_stok)) {
                                        if ($tipe == 1) {
                                            $area_stok->jumlah = $area_stok->jumlah - $list_jumlah[$k]['jumlah'];
                                        } else {
                                            $area_stok->jumlah = $area_stok->jumlah + $list_jumlah[$k]['jumlah'];
                                        }
    
                                        $area_stok->save();
                                    } else {
                                        $area_stok = new AreaStok;
                                        $area_stok->id_area = $id_area;
                                        $area_stok->id_material = $produk;
                                        $area_stok->tanggal = date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']));
                                        $area_stok->jumlah = $list_jumlah[$k]['jumlah'];
                                        $area_stok->save();
                                    }
    
                                    $material_trans = new MaterialTrans;
    
                                    $array = [
                                        'id_material'           => $produk,
                                        'id_aktivitas_harian'   => $wannaSave->id,
                                        'tanggal'               => date('Y-m-d H:i:s'),
                                        'tipe'                  => $tipe,
                                        'jumlah'                => $list_jumlah[$k]['jumlah'],
                                        'status_produk'         => $status_produk,
                                        'id_area_stok'          => $area_stok->id,
                                    ];
    
                                    $material_trans->create($array);
    
                                    (new AktivitasHarianArea)->create([
                                        'id_aktivitas_harian'   => $wannaSave->id,
                                        'id_area_stok'          => $area_stok->id,
                                        'jumlah'                => $list_jumlah[$k]['jumlah'],
                                        'tipe'                  => $tipe,
                                        'created_at'            => date('Y-m-d H:i:s'),
                                        'created_by'            => $res_user->id,
                                    ]);
                                }
                            }
                        }
                    }
                } else { //jika tidak pengaruh tanggal produksi tidak dicentang
                    $list_produk = $req->input('list_produk');
    
                    if (!empty($list_produk)) {
                        $jums_list_produk = count($list_produk);
    
                        for ($i = 0; $i < $jums_list_produk; $i++) {
                            $produk = $list_produk[$i]['produk'];
                            $status_produk = $list_produk[$i]['status_produk'];
    
                            $list_area = $list_produk[$i]['list_area'];
                            if (!empty($list_area)) {
                                $jums_list_area = count($list_area);
                                for ($j = 0; $j < $jums_list_area; $j++) {
                                    $tipe = $list_area[$j]['tipe'];
                                    $id_area = $list_area[$j]['id_area_stok'];
                                    $list_jumlah = $list_area[$j]['list_jumlah'];
                                    $jums_list_jumlah = count($list_jumlah);
                                    // for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                    //     $area_stok = AreaStok::where('id_area', $id_area)
                                    //         ->where('id_material', $produk)
                                    //         ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                    //         ->first();
    
                                    //     if (!empty($area_stok)) {
                                    //         $wannaSave->forceDelete();
                                    //         $aktivitasHarian->approve = null;
                                    //         $aktivitasHarian->save();
    
                                    //         $this->responseCode = 403;
                                    //         $this->responseMessage = 'Area, Produk, dan Tanggal yang Anda masukkan sudah dipakai oleh data lain! Silahkan masukkan data yang lain atau hubungi Kepala Regu!';
                                    //         $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                    //         return response()->json($response, $this->responseCode);
                                    //     }
                                    // }
    
                                    for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                        $area_stok = AreaStok::where('id_area', $id_area)
                                            ->where('id_material', $produk)
                                            ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                            ->first();

                                        if (empty($area_stok)) {
                                            $area_stok = new AreaStok();
                                        }

                                        if ($area_stok->jumlah > $list_jumlah[$k]['jumlah']) {
                                            $area_stok->jumlah = $area_stok->jumlah - $list_jumlah[$k]['jumlah'];
                                        } else {
                                            AktivitasHarian::find($aktivitasHarian->id)->forceDelete();

                                            $temp_area = Area::find($id_area);
                                            $temp_material = Material::find($produk);

                                            $this->responseCode     = 500;
                                            $this->responseMessage  = 'Jumlah yang Anda masukkan pada area ' . $temp_area->nama . ' dengan nama material ' . $temp_material->nama . ' melebihi jumlah ketersediaan!';
                                            $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                            return response()->json($response, $this->responseCode);
                                        }
    
                                        // $arr = [
                                        //     'id_material'   => $produk,
                                        //     'id_area'       => $id_area,
                                        //     'jumlah'        => $list_jumlah[$k]['jumlah'],
                                        //     'tanggal'       => date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])),
                                        // ];

                                        $area_stok->id_material   = $produk;
                                        $area_stok->id_area       = $id_area;
                                        $area_stok->tanggal       = date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']));

                                        $area_stok->save();
    
                                        // $saved_area_stok = $area_stok->create($arr);
    
                                        $material_trans = new MaterialTrans;
    
                                        $array = [
                                            'id_material'           => $produk,
                                            'id_aktivitas_harian'   => $aktivitasHarian->id,
                                            'tanggal'               => date('Y-m-d H:i:s'),
                                            'tipe'                  => $tipe,
                                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                                            'status_produk'         => $status_produk,
                                            'id_area_stok'          => $area_stok->id,
                                        ];
                                        $material_trans->create($array);
    
                                        (new AktivitasHarianArea)->create([
                                            'id_aktivitas_harian'   => $aktivitasHarian->id,
                                            'id_area_stok'          => $area_stok->id,
                                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                                            'tipe'                  => $tipe,
                                            'created_at'            => date('Y-m-d H:i:s'),
                                            'created_by'            => $res_user->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
    
                //simpan pallet (stok=1, dipakai=2, kosong=3, rusak=4)
                $list_pallet = $req->input('list_pallet');
                if (!empty($list_pallet)) {
                    $jums_list_pallet = count($list_pallet);
    
                    for ($i = 0; $i < $jums_list_pallet; $i++) {
                        $pallet = $list_pallet[$i]['pallet'];
                        $jumlah = $list_pallet[$i]['jumlah'];
                        $status_pallet = $list_pallet[$i]['status_pallet'];
                        $tipe = $list_pallet[$i]['tipe'];
                        $arr = [
                            'id_aktivitas_harian'       => $wannaSave->id,
                            'tanggal'                   => date('Y-m-d H:i:s'),
                            'id_material'               => $pallet,
                            'jumlah'                    => $jumlah,
                            'tipe'                      => $tipe,
                            'status_pallet'             => $status_pallet,
                        ];
    
                        $materialTrans = new MaterialTrans;
    
                        $materialTrans->create($arr);
    
                        $gudangStok = GudangStok::where('id_gudang', $gudang->id)->where('id_material', $pallet)->first();
    
                        if (empty($gudangStok)) {
                            $gudangStok = new GudangStok;
                        }
    
                        $gudangStok->id_gudang     = $gudang->id;
                        $gudangStok->id_material   = $pallet;
                        $gudangStok->jumlah        = $jumlah;
                        $gudangStok->status        = $status_pallet;
                        $gudangStok->save();
                    }
                }
    
                if ($aktivitasGudang->aktivitas->penerimaan_gi != null) {
                    $tkbm = TenagaKerjaNonOrganik::findOrFail($res_user->id_tkbm);
                    $message = 'Pengiriman Gudang Internal pada gudang '. $gudang->nama.' berhasil di setujui oleh '.$tkbm->nama;
                    $this->storeNotification($aktivitasHarian, $message, true);
                }
    
                $this->responseCode = 200;
                $this->responseData = [
                    'data'      => $wannaSave, 
                    'produk'    => $produk, 
                    'pallet'    => $pallet, 
                ];
                $this->responseMessage = 'Data berhasil disimpan!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'Aktivitas tidak berstatus penerimaan GI!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Aktivitas tidak terdaftar pada gudang Anda saat ini!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

    }

    public function getAktivitas()
    {
        $gudang = $this->getCheckerGudang();

        $res=  AktivitasGudang::where('id_gudang', $gudang->id)->get();
        foreach ($res as $key) {
            $aktivitas = Aktivitas::find($key->id_aktivitas);

            if ($aktivitas->penerimaan_gi != null) {
                $this->responseCode = 200;
                $this->responseData = $aktivitas;
                $this->responseMessage = '';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
        }

        $this->responseCode = 404;
        $this->responseMessage = 'Aktivitas penerimaan GI tidak ditemukan';
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function loadPenerimaan($id) //memuat nama material apa saja yang telah dikirim oleh gudang sebalah
    {
        $aktivitasHarian = AktivitasHarian::findOrFail($id);
        $aktivitasGudang = AktivitasGudang::where('id_aktivitas', $aktivitasHarian->id_aktivitas)->get();
        foreach ($aktivitasGudang as $key => $value) {
            $aktivitas = Aktivitas::findOrFail($aktivitasHarian->id_aktivitas);
            if ($aktivitas->internal_gudang == null) {
                return response()->json([
                    'data' => [],
                    'status' => [
                        'message' => 'Aktivitas tidak valid!',
                        'code' => Response::HTTP_FORBIDDEN
                    ]
                ], Response::HTTP_FORBIDDEN);
            }
        }
        $produk = MaterialTrans::select(
            'material_trans.id_material',
            'nama as nama_material',
            'material_trans.tipe',
            'material_trans.jumlah',
            'area_stok.tanggal',
            'area_stok.id_area'
        )
        ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
        ->leftJoin('area_stok', 'area_stok.id', '=', 'material_trans.id_area_stok')
        ->where('id_aktivitas_harian', $id)
        ->whereNotNull('status_produk')
        ->get();

        $pallet = MaterialTrans::select(
            'id_material',
            'nama as nama_material',
            'tipe',
            'jumlah'
        )
        ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
        ->where('id_aktivitas_harian', $id)
        ->whereNotNull('status_pallet')
        ->get();
        
        return response()->json([
        'data' => [
            'produk' => $produk,
            'pallet' => $pallet
        ],
        'status' => [
            'message' => '',
            'code' => Response::HTTP_OK
        ]
        ], Response::HTTP_OK);
    }

    public function getAreaFromPengirim($id) //memuat area apa saja dan jumlahnya berapa dari si pengirim
    {
        $aktivitasHarian = AktivitasHarian::find($id);
        $data = [];
        if (!empty($aktivitasHarian)) {
            $aktivitas = Aktivitas::whereNotNull('penerimaan_gi')->first();

            $aktivitasHarianArea = AktivitasHarianArea::where('id_aktivitas_harian', $aktivitasHarian->id)->get();

            $data = AreaPenerimaanGiResource::collection($aktivitasHarianArea);
        }

        return response()->json([
            'data' => $data,
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ]
        ], Response::HTTP_OK);
    }

    public function listTanggalFromAreaStok(Request $req, $idArea) //memuat daftar tanggal yang tersedia pada gudang ini pada mode penerimaan GI
    {
        $user = $req->get('my_auth');
        $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
            ->where('id_tkbm', $user->id_tkbm)
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

        $data = AreaStok::
        leftJoin('area', 'area.id', '=', 'area_stok.id_area')
        ->where('id_gudang', $gudang->id)
        ->where('id_area', $idArea)->orderBy('tanggal', 'asc')->get();
        return response()->json([
            'data' => $data,
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ]
        ], Response::HTTP_OK);
    }

    public function getAreaFromPenerima(Request $req)
    {
        $user = $req->get('my_auth');
        $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
            ->where('id_tkbm', $user->id_tkbm)
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
        $data = Area::select(
            'area.id',
            'id_gudang',
            'nama',
            'area_stok.tanggal',
            'kapasitas',
            'jumlah'
        )
        ->leftJoin('area_stok', 'area_stok.id_area', '=', 'area.id')
        ->where('id_gudang', $gudang->id)->get();
        return response()->json([
            'data' => $data,
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ]
        ], Response::HTTP_OK);
    }

    public function show(Aktivitas $aktivitas) //menampilkan detail aktivitas
    {
        return (new AktivitasResource($aktivitas))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getJenisFoto(Request $req) //memuat jenis foto yang tersedia pada aktivitas bersangkutan
    {
        $id_aktivitas = $req->input('id_aktivitas');
        $resource = AktivitasMasterFoto::select(
            'id_foto_jenis',
            'foto_jenis.nama',
            'id_aktivitas'
        )
        ->join('foto_jenis', 'id_foto_jenis', '=', 'foto_jenis.id')
        ->where('id_aktivitas', $id_aktivitas)->get();
        return (new AktivitasResource($resource))->additional([
            'url' => '{{base_url}}/watch/{{foto}}?token={{token}}&un={{id_aktivitas_harian}}&ctg=kelayakan&src={{file_enc}}',
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getMuatanFoto(Request $req) //memuat kelayakan foto
    {
        $id_aktivitas_harian = $req->input('id_aktivitas_harian');
        $resource = AktivitasFoto::select(
            'id',
            'jenis',
            'foto',
            'size',
            'ekstensi',
            'file_enc'
        )
        ->with('foto_jenis')
        ->where('id_aktivitas_harian', $id_aktivitas_harian)->get();
        return (new AktivitasResource($resource))->additional([
            'url' => '{{base_url}}/watch/{{foto}}?token={{token}}&un={{id_aktivitas_harian}}&ctg=kelayakan&src={{file_enc}}',
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getKelayakanFoto(Request $req) //memuat kelayakan foto
    {
        $id_aktivitas_harian = $req->input('id_aktivitas_harian');
        $resource = AktivitasKelayakanFoto::select(
            'id',
            'jenis',
            DB::raw('CASE WHEN jenis = 1 THEN \'Before Kelayakan\' ELSE \'After Kelayakan\' END AS text_jenis'),
            'foto',
            'size',
            'ekstensi',
            'file_enc'
        )
            ->where('id_aktivitas_harian', $id_aktivitas_harian)->get();
        return (new AktivitasResource($resource))->additional([
            'url' => '{{base_url}}/watch/{{foto}}?token={{token}}&un={{id_aktivitas_harian}}&ctg=kelayakan&src={{file_enc}}',
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getYayasan()
    {
        $search = strip_tags(request()->input('search'));
        $resource = Yayasan::where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function areaStok() //memuat area stok 
    {
        $area = Area::join('area_stok', 'area.id', '=', 'area_stok.id_area')->orderBy('area_stok.tanggal', 'asc')
            ->get();

        collect($area)->groupBy('tanggal');

        return $area->toArray();
    }

    public function storePengembalian(ApiAktivitasPengembalianRequest $req)
    {
        $req->validated();
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $aktivitasHarian = AktivitasHarian::findOrFail($req->input('id_aktivitas_harian'));
        $gudang = $this->getCheckerGudang();

        if ($aktivitasHarian->dikembalikan != null) {
            $this->responseCode = 403;
            $this->responseMessage = 'Peminjaman sudah diselesaikan!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        $shiftKerja = new ShiftKerja;

        $wannaSave = new AktivitasHarian;
        $wannaSave->ref_number        = $aktivitasHarian->id;
        $wannaSave->id_aktivitas      = $aktivitasHarian->id_aktivitas;
        $wannaSave->id_gudang         = $gudang->id;
        // $wannaSave->id_karu           = $gudang->id_karu;
        // $wannaSave->id_shift          = $rencana_tkbm->id_shift;
        $wannaSave->id_area           = $req->input('id_pindah_area');
        $wannaSave->id_alat_berat     = $req->input('id_alat_berat');
        $wannaSave->sistro            = $req->input('sistro');
        $wannaSave->alasan            = $req->input('alasan');
        $wannaSave->created_by        = $res_user->id;
        $wannaSave->created_at        = date('Y-m-d H:i:s');

        $wannaSave->save();

        $aktivitasHarian->dikembalikan = date('Y-m-d H:i:s');
        $aktivitasHarian->save();

        $list_produk = $req->input('list_produk');
        $produk = [];

        if (!empty($list_produk)) {
            $jums_list_produk = count($list_produk);

            for ($i = 0; $i < $jums_list_produk; $i++) {
                $produk = $list_produk[$i]['produk'];
                $status_produk = $list_produk[$i]['status_produk'];
                $list_area = $list_produk[$i]['list_area'];
                $jums_list_area = count($list_area);

                for ($j = 0; $j < $jums_list_area; $j++) {
                    $tipe = $list_area[$j]['tipe'];
                    $id_area = $list_area[$j]['id_area_stok'];
                    $list_jumlah = $list_area[$j]['list_jumlah'];
                    $jums_list_jumlah = count($list_jumlah);

                    for ($k = 0; $k < $jums_list_jumlah; $k++) {
                        $area_stok = AreaStok::where('id_area', $id_area)
                            ->where('id_material', $produk)
                            ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                            ->first();

                        if (!empty($area_stok)) {
                            if ($tipe == 1) {
                                $area_stok->jumlah = $area_stok->jumlah - $list_jumlah[$k]['jumlah'];
                            } else {
                                $area_stok->jumlah = $area_stok->jumlah + $list_jumlah[$k]['jumlah'];
                            }

                            $area_stok->save();
                        } else {
                            $area_stok = new AreaStok;
                            $area_stok->id_area = $id_area;
                            $area_stok->id_material = $produk;
                            $area_stok->tanggal = date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']));
                            $area_stok->jumlah = $list_jumlah[$k]['jumlah'];
                            $area_stok->save();
                        }

                        $material_trans = new MaterialTrans;

                        $array = [
                            'id_material'           => $produk,
                            'id_aktivitas_harian'   => $wannaSave->id,
                            'tanggal'               => date('Y-m-d H:i:s'),
                            'tipe'                  => $tipe,
                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                            'status_produk'         => $status_produk,
                            'id_area_stok'          => $area_stok->id,
                        ];

                        $material_trans->create($array);

                        (new AktivitasHarianArea)->create([
                            'id_aktivitas_harian'   => $wannaSave->id,
                            'id_area_stok'          => $area_stok->id,
                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                            'tipe'                  => $tipe,
                            'created_at'            => date('Y-m-d H:i:s'),
                            'created_by'            => $res_user->id,
                        ]);
                    }
                }
            }
        }

        //simpan pallet (stok=1, dipakai=2, kosong=3, rusak=4)
        $list_pallet = $req->input('list_pallet');
        if (!empty($list_pallet)) {
            $jums_list_pallet = count($list_pallet);

            for ($i = 0; $i < $jums_list_pallet; $i++) {
                $pallet = $list_pallet[$i]['pallet'];
                $jumlah = $list_pallet[$i]['jumlah'];
                $status_pallet = $list_pallet[$i]['status_pallet'];
                $tipe = $list_pallet[$i]['tipe'];
                $arr = [
                    'id_aktivitas_harian'       => $wannaSave->id,
                    'tanggal'                   => date('Y-m-d H:i:s'),
                    'id_material'               => $pallet,
                    'jumlah'                    => $jumlah,
                    'tipe'                      => $tipe,
                    'status_pallet'             => $status_pallet,
                ];

                $materialTrans = new MaterialTrans;

                $materialTrans->create($arr);

                $gudangStok = GudangStok::where('id_gudang', $gudang->id)->where('id_material', $pallet)->first();

                if (empty($gudangStok)) {
                    $gudangStok = new GudangStok;
                }

                $gudangStok->id_gudang     = $gudang->id;
                $gudangStok->id_material   = $pallet;
                $gudangStok->jumlah        = $jumlah;
                $gudangStok->status        = $status_pallet;
                $gudangStok->save();
            }
        }

        $this->responseCode = 200;
        $this->responseData = [
            'data'      => $wannaSave,
        ];

        // dd($produk);

        if ($produk != null) {
            $this->responseData['produk']    = $produk;
        }
        if ($pallet != null) {
            $this->responseData['pallet']    = $pallet;
        }
        $this->responseMessage = 'Data berhasil disimpan!';
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function history(Request $req) //memuat history
    {
        $gudang = $this->getCheckerGudang();
        $search = $req->input('search');
        $my_auth = $req->get('my_auth');
        $user = Users::findOrFail($my_auth->id_user);

        $res = AktivitasHarian::select(
            'aktivitas_harian.id',
            'aktivitas.id as id_aktivitas',
            'aktivitas.nama as nama_aktivitas',
            'gudang.nama as nama_gudang',
            'peminjaman',
            'dikembalikan',
            'draft',
            DB::raw('CASE WHEN approve IS NOT NULL OR internal_gudang IS NULL THEN \'Done\' ELSE \'Progress\' END AS text_status'),
            DB::raw('CASE WHEN dikembalikan IS NOT NULL THEN \'Done\' ELSE \'Progress\' END AS text_peminjaman'),
            'aktivitas_harian.created_at',
            'aktivitas_harian.created_by'
        )
            ->join('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
            ->join('gudang', 'aktivitas_harian.id_gudang', '=', 'gudang.id')
            ->where(function ($where) use ($gudang) {
                $where->where('id_gudang', $gudang->id);
                $where->orWhere('id_gudang_tujuan', $gudang->id);
            })
            ->whereNull('ref_number')
            ->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(aktivitas.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(DB::raw('LOWER(gudang.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->orderBy('created_at', 'desc');

        $obj =  AktivitasResource::collection($res->paginate(10))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function detailHistory($id) //memuat detail history
    {
        $gudang = $this->getCheckerGudang();
        $my_auth = request()->get('my_auth');
        $user = Users::findOrFail($my_auth->id_user);

        $res = AktivitasHarian::select(
            'aktivitas_harian.id',
            'aktivitas_harian.id_aktivitas',
            'aktivitas.nama as nama_aktivitas',
            'aktivitas.produk_stok',
            'aktivitas.produk_rusak',
            'aktivitas.pallet_stok',
            'aktivitas.pallet_dipakai',
            'aktivitas.pallet_kosong',
            'aktivitas.pallet_rusak',
            'nomor_lambung',
            'sistro',
            'internal_gudang',
            'ttd',
            'id_gudang',
            'id_alat_berat',
            'approve',
            'id_gudang_tujuan',
            'aktivitas_harian.so',
            'id_yayasan',
            'aktivitas.peminjaman',
            'aktivitas_harian.dikembalikan',
            'id_tkbm',
            'alasan',
            'ttd',
            'draft',
            DB::raw('(SELECT nama gudang FROM gudang WHERE id = id_gudang)
                    AS text_gudang'),
            DB::raw('(SELECT nama FROM alat_berat_kat WHERE id = id_kategori)
                    AS kategori'),
            DB::raw('(SELECT nama FROM yayasan WHERE id = id_yayasan)
                    AS text_yayasan'),
            DB::raw('(SELECT nama gudang FROM gudang WHERE id = id_gudang)
                    AS text_gudang_asal'),
            'id_gudang_tujuan',
            DB::raw('(SELECT nama gudang FROM gudang WHERE id = id_gudang_tujuan)
                    AS text_gudang_tujuan'),
            'butuh_approval',
            DB::raw('
                CASE
                    WHEN internal_gudang IS NOT NULL AND butuh_approval IS NOT NULL THEN true AND id_gudang_tujuan = '.$gudang->id.'
                ELSE false
            END AS tombol_approval'),
            DB::raw('
                CASE 
                WHEN pindah_area IS NOT NULL AND internal_gudang IS NOT NULL THEN
                    \'Pindah Area\'
                WHEN internal_gudang IS NOT NULL THEN
                    \'Pengiriman Gudang Internal\'
                WHEN pengiriman IS NOT NULL THEN
                    \'Pengiriman GP\'
                WHEN peminjaman IS NOT NULL THEN
                    \'Peminjaman\'
            END AS jenis_aktivitas'),
            DB::raw('CASE WHEN dikembalikan IS NOT NULL THEN \'Done\' ELSE \'Progress\' END AS text_peminjaman'),
            DB::raw('CASE WHEN approve IS NOT NULL OR internal_gudang IS NULL THEN \'Done\' ELSE \'Progress\' END AS text_status'),
            'aktivitas_harian.created_at',
            'aktivitas_harian.created_by' 
        )
        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
        ->leftJoin('alat_berat', 'aktivitas_harian.id_alat_berat', '=', 'alat_berat.id')
        ->where('aktivitas_harian.id', $id)
        ->orderBy('aktivitas_harian.id', 'desc')
        ;

        $res_produk = MaterialTrans::select(
            'material.id as id_material',
            'material.nama as nama_material',
            'tipe',
            'status_produk',
            DB::raw('CASE WHEN status_produk=1 THEN \'Produk Stok\' ELSE \'Produk Rusak\' END AS text_status_produk'),
            DB::raw('CASE WHEN tipe=1 THEN \'Mengurangi\' ELSE \'Menambah\' END AS text_tipe'),
            'jumlah'
        )
        ->join('material', 'material_trans.id_material', '=', 'material.id')
        ->where('id_aktivitas_harian', $id)
        ->where('kategori', 1)
        ->orderBy('status_produk', 'asc')
        ->get();

        $res_pallet = MaterialTrans::select(
            'material.id as id_material',
            'material.nama as nama_material',
            'tipe',
            'status_pallet',
            DB::raw('CASE WHEN status_pallet=1 THEN \'Pallet Stok\' WHEN status_pallet=2 THEN \'Pallet Dipakai\' WHEN status_pallet=3 THEN \'Pallet Kosong\' ELSE \'Pallet Rusak\' END AS text_status_pallet'),
            DB::raw('CASE WHEN tipe=1 THEN \'Mengurangi\' ELSE \'Menambah\' END AS text_tipe'),
            'jumlah'
        )
        ->join('material', 'material_trans.id_material', '=', 'material.id')
        ->where('id_aktivitas_harian', $id)
        ->where('kategori', 2)
        ->whereNotNull('status_pallet')
        ->orderBy('material.nama', 'asc')
        ->get();

        $foto = AktivitasFoto::select(
            'aktivitas_foto.id',
            'id_aktivitas_harian',
            'id_foto_jenis',
            'foto_jenis.nama as nama_jenis',
            'foto',
            'size',
            'lat',
            'lng'
        )
        ->join('foto_jenis', 'id_foto_jenis', '=', 'foto_jenis.id')
        ->where('id_aktivitas_harian', $id)
        ->orderBy('id_foto_jenis', 'asc')
        ->get();

        $list_alat_berat = AktivitasHarianAlatBerat::select(
            'alat_berat.*',
            'alat_berat_kat.nama as nama_kategori'
        )
        ->join('alat_berat', 'aktivitas_harian_alat_berat.id_alat_berat', '=', 'alat_berat.id')
        ->join('alat_berat_kat', 'alat_berat.id_kategori', '=', 'alat_berat_kat.id')
        ->where('alat_berat.status', 1)
        ->get();

        $obj = (new AktivitasResource($res->get()))->additional([
            'produk' => $res_produk,
            'pallet' => $res_pallet,
            'alat_berat' => $list_alat_berat,
            'file' => $foto,
            'url' => '{{base_url}}/watch/{{foto}}?token={{token}}&un={{id_aktivitas_harian}}&ctg=aktivitas_harian&src={{foto}}',
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function historyMaterialArea($id_aktivitas_harian, $id_material)
    {
        $res = AktivitasHarianArea::with('areaStok')
        ->where('id_aktivitas_harian', $id_aktivitas_harian)
        ->whereHas('areaStok', function ($query) use ($id_material) {
            $query->where('id_material', $id_material);
        });

        $aktivitasHarian = AktivitasHarian::where('ref_number', $id_aktivitas_harian)->first();
        // $resApprove = [];
        // if (!empty($aktivitasHarian)) {
        //     $resApprove = AktivitasHarianArea::with('areaStok')
        //         ->where('id_aktivitas_harian', $aktivitasHarian->id)
        //         ->whereHas('areaStok', function ($query) use ($id_material) {
        //             $query->where('id_material', $id_material);
        //         })->get();
        // }

        $obj = HistoryMaterialAreaResource::collection($res->get())->additional([
            // 'dataApprove' => HistoryMaterialAreaResource::collection($resApprove),
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function getAlat()
    {
        $data = (new AlatBerat)->with('kategori')->get();
        return AlatBeratResource::collection($data);
    }

    public function getDataSistro()
    {
        $res = Sistro::take(100)->get();

        return $res;
    }
    
    public function getSistro(Request $req)
    {
        $tiketnumber = $req->input('tiketnumber');
        $sistro = Sistro::where('tiketno', $tiketnumber)->first();
        $res = Material::with('sistro')->where('id_material_sap', $sistro->idproduk)->get();

        $obj = GetSistroResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function testFirebase()
    {
        // send_firebase('');
    }

    public function listNotifikasi()
    {
        $gudang = $this->getCheckerGudang();
        $res = AktivitasHarian::with(['aktivitas', 'gudang', 'gudangTujuan'])->whereHas('aktivitas', function ($query) {
            $query->whereNotNull('internal_gudang');
        })->where('id_gudang_tujuan', $gudang->id)->get();

        $obj = ListNotifikasiResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function testNotif(AktivitasHarian $aktivitasHarian) //save notifikasi, for testing only
    {
        // $gudang = Gudang::findOrFail($aktivitasHarian->id_gudang_tujuan);
        // $gudang->notify(new Pengiriman($aktivitasHarian));
        $gudang = Gudang::findOrFail($aktivitasHarian->id_gudang_tujuan);
        // $isiNotif = ;
        $gudang->notify(new Pengiriman($aktivitasHarian));

        $aktivitas = Aktivitas::find($aktivitasHarian->id_aktivitas);

        $rencanaHarian = RencanaHarian::where('id_gudang', $aktivitasHarian->id_gudang_tujuan)
            ->orderBy('id', 'desc')
            ->first();

        $rencanaTkbm = RencanaTkbm::where('id_rencana', $rencanaHarian->id)
            ->join('rencana_harian', 'rencana_tkbm.id_rencana', '=', 'rencana_harian.id')
            ->where(function ($query) {
                $query->where('end_date');
                $query->orWhere('end_date', '>', now());
            })
            ->get();
        // dd($rencanaTkbm->toArray());
        // dd($user);
        foreach ($rencanaTkbm as $key) {
            $user = Users::where('id_tkbm', $key->id_tkbm)->first();
            if (!empty($user)) {
                $res = send_firebase(
                    $user->user_gcid,
                    [
                        'title' => $aktivitas->nama,
                        'message' => 'Ada pengiriman dari ' . $aktivitasHarian->gudang->nama . ' dengan nama ' . $aktivitas->nama,
                        'meta' => [
                            'id' => $aktivitasHarian->id,
                            'id_aktivitas' => $aktivitasHarian->id_aktivitas,
                            'kode_aktivitas' => $aktivitasHarian->kode_aktivitas,
                        ],
                    ]
                );
            }
        }

        // send_firebase(
        //     'dQMTBNjR6RU:APA91bF2DD6hvuUpEDerEI5I6EL26-rDoAnehdDp5HG3ie3pQZLpW5fTdT4a2Llu6Tz372iZWqTGq8ng1xnEXa055gswUVg9U2wQxPiQh1u_ghBNAKWK07rrHP1r-6ZVzRZPUkRnsW99',
        //     [
        //         'title' => 'warkop',
        //         'message' => 'yohoho',
        //         'meta' => [
        //             'id' => $aktivitasHarian->id,
        //             'id_aktivitas' => $aktivitasHarian->id_aktivitas,
        //             'kode_aktivitas' => $aktivitasHarian->kode_aktivitas,
        //         ],
        //     ]
        // );
    }

    public function allNotif()
    {
        $gudang = Gudang::find($this->getCheckerGudang()->id);
        $this->responseCode = 200;
        $this->responseData = $gudang->notifications;
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function unreadNotif()
    {
        $gudang = Gudang::find($this->getCheckerGudang()->id);
        $this->responseCode = 200;
        $this->responseData = $gudang->unreadNotifications;
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function readNotif()
    {
        $gudang = Gudang::find($this->getCheckerGudang()->id);
        $this->responseCode = 200;
        $this->responseData = $gudang->readNotifications;
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function markAsRead(Request $request)
    {
        if ($request->has('read')) {
            $gudang = Gudang::findOrFail($this->getCheckerGudang()->id);

            $notification = $gudang->notifications()->where('id', $request->read)->first();
            if ($notification) {
                $notification->markAsRead();

                $this->responseCode = 200;
                $this->responseData = $notification;
                $this->responseMessage = 'Berhasil ditandai!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }

            $this->responseCode = 500;
            $this->responseMessage = 'Gagal ditandai!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
    }

    public function testSave(KategoriAlatBerat $kategoriAlatBerat)
    {
        $nomor_lambung = request()->nomor_lambung;

        $alatBerat = new AlatBerat(['nomor_lambung' => $nomor_lambung, 'created_by' =>auth()->id()]);

        $kategoriAlatBerat->alatBerat()->save($alatBerat);
    }

    public function isiStok($hapus=false)
    {
        
        $material = Material::produk()->get();
        $area = Area::all();

        if ($hapus) {
            AreaStok::truncate();
        }

        foreach ($area as $keyArea) {
            foreach ($material as $keyMaterial) {
                $areaStok = new AreaStok;
                $areaStok->fill([
                    'id_area'       => $keyArea->id,
                    'id_material'   => $keyMaterial->id,
                    'tanggal'       => '2019-12-20',
                    'jumlah'        => 100
                ])->save();

                $areaStok = new AreaStok;
                $areaStok->fill([
                    'id_area'       => $keyArea->id,
                    'id_material'   => $keyMaterial->id,
                    'tanggal'       => '2019-12-25',
                    'jumlah'        => 100
                ])->save();

                // $areaStok->materialTrans()->saveMany([
                //     new MaterialTrans()
                // ]);
            }
        }
    }
}
