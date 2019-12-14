<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\Users;
use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasGudang;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasHarianArea;
use App\Http\Models\AktivitasKelayakanFoto;
use App\Http\Models\AktivitasMasterFoto;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\Sistro;
use App\Http\Requests\ApiAktivitasPenerimaanGiRequest;
use App\Http\Requests\ApiAktivitasRequest;
use App\Http\Requests\ApiSaveKelayakanPhotos;
use App\Http\Requests\ApiSavePhotosRequest;
use App\Http\Resources\AktivitasHarianResource;
use App\Http\Resources\AktivitasResource;
use App\Http\Resources\AlatBeratResource;
use App\Http\Resources\AreaPenerimaanGiResource;
use App\Http\Resources\AreaStokResource;
use App\Http\Resources\getAreaFromPenerimaResource;
use App\Http\Resources\HistoryMaterialAreaResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AktivitasController extends Controller
{
    private function getCheckerGudang($user) { //untuk memperoleh informasi checker ini sekarang berada di gudang mana
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

        return $gudang->id;
    }

    public function index(Request $req) //memuat daftar aktivitas
    {
        $search = strip_tags($req->input('search'));
        $user = $req->get('my_auth');
        $id_gudang = $this->getCheckerGudang($user);
        $obj =  AktivitasResource::collection(AktivitasGudang::
        join('aktivitas', 'aktivitas.id', '=', 'aktivitas_gudang.id_aktivitas')
        ->where('id_gudang', $id_gudang)
        ->where(function ($where) use ($search) {
            $where->where(DB::raw('nama'), 'ILIKE', '%' . strtolower($search) . '%');
        })->orderBy('id', 'desc')->paginate(10))->additional([
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

    public function getArea(Request $req, $id_aktivitas, $id_material) //memuat area
    {
        $user = $req->get('my_auth');
        $id_gudang = $this->getCheckerGudang($user);

        $search = strip_tags($req->input('search'));
        $aktivitas = Aktivitas::findOrFail($id_aktivitas);
        
        if ($aktivitas->pengaruh_tgl_produksi != null) {
            $resource = Area::
            select(
                'area.id',
                'area.nama',
                'area.kapasitas',
                'area_stok.tanggal',
                DB::raw('(SELECT sum(jumlah) FROM area_stok where id_area = area.id and id_material = '.$id_material.') as jumlah')
            )
            ->join('area_stok', 'area_stok.id_area', '=', 'area.id')
            ->where('id_material', $id_material)
            ->where('id_gudang', $id_gudang)
            ->get();
        } else {
            $resource = Area::select(
                'area.id',
                'area.nama',
                'area.kapasitas',
                DB::raw("TO_CHAR(now(),'YYYY-MM-DD') as tanggal"),
                DB::raw('0 as jumlah')
            )
            ->where(function ($where) use ($search) {
                $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->where('id_gudang', $id_gudang)
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

    public function getAlatBerat(Request $req) //memuat alat berat
    {
        $search = strip_tags($req->input('search'));
        $resource = AlatBerat::
        leftJoin('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
        ->where('status', 1)
        ->where(function ($where) use ($search) {
            $where->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            $where->orWhere(DB::raw('LOWER(nomor_lambung)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
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
        ->where('tanggal', date('Y-m-d', strotime($tanggal)))
        ->first();
        if (!empty($areaStok)) {
            
        }
    }

    public function store(ApiAktivitasRequest $req, AktivitasHarian $aktivitasHarian) //menyimpan aktivitas harian secara reguler
    {
        $req->validated();

        $user       = $req->get('my_auth');
        $res_user   = Users::findOrFail($user->id_user);

        if ($res_user->role_id == 3) { //hanya checker yang diizinkan untuk menambah aktivitas harian
            $rencana_tkbm = RencanaTkbm::leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
                ->where('id_tkbm', $user->id_tkbm)
                ->orderBy('rencana_harian.id', 'desc')
                ->take(1)->first();

            if (empty($rencana_tkbm)) {
                $this->responseCode     = 500;
                $this->responseMessage  = 'Checker tidak terdaftar pada rencana harian apapun!';
                $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
            $rencana_harian = RencanaHarian::findOrFail($rencana_tkbm->id_rencana);
            $gudang = Gudang::findOrFail($rencana_harian->id_gudang);
            if (empty($gudang)) {
                $this->responseCode     = 500;
                $this->responseMessage  = 'Gudang tidak tersedia!';
                $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }

            //simpan aktivitas
            $aktivitasHarian->id_aktivitas      = $req->input('id_aktivitas');
            $aktivitasHarian->id_gudang         = $gudang->id;
            $aktivitasHarian->id_karu           = $gudang->id_karu;
            $aktivitasHarian->id_shift          = $rencana_tkbm->id_shift;
            $aktivitasHarian->id_gudang_tujuan  = $req->input('id_gudang_tujuan');
            $aktivitasHarian->ref_number        = $req->input('ref_number');
            $aktivitasHarian->id_alat_berat     = $req->input('id_alat_berat');
            $aktivitasHarian->sistro            = $req->input('sistro');
            $aktivitasHarian->approve           = $req->input('approve');
            $aktivitasHarian->kelayakan_before  = $req->input('kelayakan_before');
            $aktivitasHarian->kelayakan_after   = $req->input('kelayakan_after');
            $aktivitasHarian->dikembalikan      = $req->input('dikembalikan');
            $aktivitasHarian->alasan            = $req->input('alasan');
            $aktivitasHarian->created_by        = $res_user->id;
            $aktivitasHarian->created_at        = date('Y-m-d H:i:s');

            $saved = $aktivitasHarian->save();

            if ($saved) {
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
                                            $area_stok->jumlah = $area_stok->jumlah-$list_jumlah[$k]['jumlah'];
                                        } else {
                                            $area_stok->jumlah = $area_stok->jumlah+$list_jumlah[$k]['jumlah'];
                                        }
                                        
                                        $area_stok->save();
                                    } else {
                                        $area_stok              = new AreaStok;
                                        $area_stok->id_area     = $id_area;
                                        $area_stok->id_material = $produk;
                                        $area_stok->tanggal     = date('Y-m-d', strtotime($list_jumlah[$k]['tanggal']));
                                        $area_stok->jumlah      = $list_jumlah[$k]['jumlah'];
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
                                            ->first();
                                            
                                        if (!empty($area_stok)) {
                                            $this->responseCode     = 403;
                                            $this->responseMessage  = 'Area, Produk, dan Tanggal yang Anda masukkan sudah dipakai oleh data lain! Silahkan masukkan data yang lain atau hubungi Kepala Regu!';
                                            $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                            return response()->json($response, $this->responseCode);
                                        }
                                    }

                                    for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                        $area_stok = new AreaStok;
    
                                        $arr = [
                                            'id_material'   => $produk,
                                            'id_area'       => $id_area,
                                            'jumlah'        => $list_jumlah[$k]['jumlah'],
                                            'tanggal'       => date('Y-m-d'),
                                        ];
    
                                        $saved_area_stok = $area_stok->create($arr);

    
                                        $material_trans = new MaterialTrans;

                                        $array = [
                                            'id_material'           => $produk,
                                            'id_aktivitas_harian'   => $aktivitasHarian->id,
                                            'tanggal'               => date('Y-m-d H:i:s'),
                                            'tipe'                  => $tipe,
                                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                                            'status_produk'         => $status_produk,
                                        ];
                                        $material_trans->create($array);
                                        
                                        (new AktivitasHarianArea)->create([
                                            'id_aktivitas_harian'   => $aktivitasHarian->id,
                                            'id_area_stok'          => $saved_area_stok->id,
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
                        $arr = [
                            'id_aktivitas_harian'       => $aktivitasHarian->id,
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


                return (new AktivitasResource($aktivitasHarian))->additional([
                    'produk' => $list_produk,
                    'pallet' => $list_pallet,
                    'status' => [
                        'message'   => '',
                        'code'      => Response::HTTP_CREATED,
                    ]
                ], Response::HTTP_CREATED);
            } else {
                $this->responseCode     = 500;
                $this->responseMessage  = 'Gagal menyimpan aktivitas!';
                $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
        } else {
            $this->responseCode     = 403;
            $this->responseMessage  = 'Hanya Checker yang diizinkan untuk menyimpan aktivitas!';
            $response               = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
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
                Storage::deleteDirectory('/public/aktivitas_harian/' . $id_aktivitas_harian);
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
            Storage::deleteDirectory('/public/aktivitas_harian/' . $id_aktivitas_harian);
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

        // return (new AktivitasResource($foto))->additional([
        //     'status' => [
        //         'message' => '',
        //         'code' => Response::HTTP_CREATED,
        //     ]
        // ], Response::HTTP_CREATED);

        return response()->json([
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

            return (new AktivitasResource($foto))->additional([
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
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $aktivitas = Aktivitas::whereNotNull('penerimaan_gi')->first();
        $aktivitasHarian = AktivitasHarian::findOrFail($req->input('id_aktivitas_harian'));

        if ($aktivitasHarian->approve != null) {
            $this->responseCode = 403;
            $this->responseMessage = 'Aktivitas harian sudah disetujui!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }

        if ($aktivitas->penerimaan_gi != null) {
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

            $wannaSave = new AktivitasHarian;
            $wannaSave->ref_number        = $aktivitasHarian->id;
            $wannaSave->id_aktivitas      = $aktivitas->id;
            $wannaSave->id_gudang         = $gudang->id;
            $wannaSave->id_karu           = $gudang->id_karu;
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

            if ($aktivitas->pengaruh_tgl_produksi != null) { //jika tidak pengaruh tanggal produksi dicentang
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
                                if ($aktivitas->fifo != null) { //jika FIFO
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
                                    'id_aktivitas_harian'   => $aktivitasHarian->id,
                                    'tanggal'               => date('Y-m-d H:i:s'),
                                    'tipe'                  => $tipe,
                                    'jumlah'                => $list_jumlah[$k]['jumlah'],
                                    'status_produk'         => $status_produk,
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
                                for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                    // dd($list_jumlah[$k]['tanggal']);
                                    $area_stok = AreaStok::where('id_area', $id_area)
                                        ->where('id_material', $produk)
                                        ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                        ->first();

                                    if (!empty($area_stok)) {
                                        $this->responseCode = 403;
                                        $this->responseMessage = 'Area, Produk, dan Tanggal yang Anda masukkan sudah dipakai oleh data lain! Silahkan masukkan data yang lain atau hubungi Kepala Regu!';
                                        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                        return response()->json($response, $this->responseCode);
                                    }
                                }

                                for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                    $area_stok = AreaStok::where('id_area', $id_area)
                                        ->where('id_material', $produk)
                                        ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                        ->first();

                                    if (!empty($area_stok)) {
                                        $this->responseCode = 403;
                                        $this->responseMessage = 'Area, Produk, dan Tanggal yang Anda masukkan sudah dipakai oleh data lain! Silahkan masukkan data yang lain atau hubungi Kepala Regu!';
                                        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                        return response()->json($response, $this->responseCode);
                                    }

                                    $area_stok = new AreaStok;

                                    $arr = [
                                        'id_material'   => $produk,
                                        'id_area'       => $id_area,
                                        'jumlah'        => $list_jumlah[$k]['jumlah'],
                                        'tanggal'       => date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])),
                                    ];

                                    $saved_area_stok = $area_stok->create($arr);

                                    $material_trans = new MaterialTrans;

                                    $array = [
                                        'id_material'           => $produk,
                                        'id_aktivitas_harian'   => $aktivitasHarian->id,
                                        'tanggal'               => date('Y-m-d H:i:s'),
                                        'tipe'                  => $tipe,
                                        'jumlah'                => $list_jumlah[$k]['jumlah'],
                                        'status_produk'         => $status_produk,
                                    ];
                                    $material_trans->create($array);

                                    (new AktivitasHarianArea)->create([
                                        'id_aktivitas_harian'   => $aktivitasHarian->id,
                                        'id_area_stok'          => $saved_area_stok->id,
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
                        'id_aktivitas_harian'       => $aktivitasHarian->id,
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
                'data' => $wannaSave, 
                'produk' => $produk, 
                'pallet' => $pallet, 
            ];
            $this->responseMessage = 'Data berhasil disimpan!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        } else {
            $this->responseCode = 500;
            $this->responseMessage = 'Gudang tidak tersedia!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
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
        // if ($aktivitas->internal_gudang != null) {
            $materialTrans = MaterialTrans::select(
                'id_material',
                'nama as nama_material',
                'jumlah'
            )
            ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
            ->where('id_aktivitas_harian', $id)
            ->whereNotNull('status_produk')
            ->get();
            
            return response()->json([
            'data' => $materialTrans,
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ]
            ], Response::HTTP_OK);
        // } else {
        //     return response()->json([
        //         'data' => [],
        //         'status' => [
        //             'message' => 'Aktivitas tidak valid!',
        //             'code' => Response::HTTP_FORBIDDEN
        //         ]
        //     ], Response::HTTP_FORBIDDEN);
        // }
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

    public function areaStok() //memuat area stok 
    {
        $area = Area::join('area_stok', 'area.id', '=', 'area_stok.id_area')->orderBy('area_stok.tanggal', 'asc')
            ->get();

        collect($area)->groupBy('tanggal');

        return $area->toArray();
    }

    public function history(Request $req) //memuat history
    {
        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);
        $rencanaTkbm = RencanaTkbm::where('id_tkbm', $res_user->id_tkbm)->orderBy('id_rencana')->first();
        $rencanaHarian = RencanaHarian::find($rencanaTkbm->id_rencana);
        $gudang = Gudang::findOrFail($rencanaHarian->id_gudang);
        $search = $req->input('search');

        $res = AktivitasHarian::select(
            'aktivitas_harian.id',
            'aktivitas.nama as nama_aktivitas',
            'gudang.nama as nama_gudang',
            DB::raw('CASE WHEN approve IS NOT NULL OR internal_gudang IS NULL THEN \'Done\' ELSE \'Progress\' END AS text_status'),
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
        ->orderBy('created_at', 'desc')
        ;

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
        $res = AktivitasHarian::select(
            'aktivitas_harian.id',
            'aktivitas.nama as nama_aktivitas',
            DB::raw('(SELECT nama gudang FROM gudang WHERE id = id_gudang)
                 AS text_gudang'),
            'nomor_lambung',
            'sistro',
            'internal_gudang',
            'ttd',
            'id_gudang_tujuan',
            DB::raw('(SELECT nama gudang FROM gudang WHERE id = id_gudang_tujuan)
                 AS text_gudang_tujuan'),
            'butuh_approval',
            DB::raw('
                CASE
                    WHEN internal_gudang IS NOT NULL AND butuh_approval IS NOT NULL THEN true
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
            DB::raw('CASE WHEN approve IS NOT NULL OR internal_gudang IS NULL THEN \'Done\' ELSE \'Progress\' END AS text_status'),
            'aktivitas_harian.created_at',
            'aktivitas_harian.created_by' 
        )
        ->leftJoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
        ->leftJoin('alat_berat', 'aktivitas_harian.id_gudang', '=', 'alat_berat.id')
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
        ->orderBy('status_pallet', 'asc')
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

        $obj = (new AktivitasResource($res->get()))->additional([
            'produk' => $res_produk,
            'pallet' => $res_pallet,
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
        $res = AktivitasHarianArea::with('areaStok')->where('id_aktivitas_harian', $id_aktivitas_harian)->whereHas('areaStok', function ($query) use ($id_material) {
            $query->where('id_material', $id_material);
        });
        // $res = AreaStok::select(
        //     'id_area',
        //     'nama',
        //     'jumlah'
        // )
        // ->leftJoin('area', 'area.id', '=', 'area_stok.id_area')
        // ->where('id_material', $id_material);

        $obj = HistoryMaterialAreaResource::collection($res->get())->additional([
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
        // return $data;
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
        $res = Material::where('id_material_sap', $sistro->idproduk)->get();

        $obj = (new AktivitasResource($res))->additional([
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
}
