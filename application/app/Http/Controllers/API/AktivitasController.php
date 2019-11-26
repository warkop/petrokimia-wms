<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasArea;
use App\Http\Models\Users;
use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasGudang;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AktivitasKelayakanFoto;
use App\Http\Models\AktivitasMasterFoto;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\GudangPallet;
use App\Http\Models\JenisFoto;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\RencanaTkbm;
use App\Http\Requests\ApiAktivitasRequest;
use App\Http\Requests\ApiSaveKelayakanPhotos;
use App\Http\Requests\ApiSavePhotosRequest;
use App\Http\Resources\AktivitasResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AktivitasController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $obj =  AktivitasResource::collection(Aktivitas::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->paginate(10))->additional([
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function getMaterial(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Material::produk()->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }
    
    public function getPallet(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Material::pallet()->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getGudang(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Gudang::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getArea(Request $req, $id_aktivitas, $id_material)
    {
        $search = strip_tags($req->input('search'));
        $aktivitas = Aktivitas::findOrFail($id_aktivitas);
        if ($aktivitas->pengaruh_tgl_produksi != null) {
            $resource = \DB::table('')
            ->select(\DB::raw('DISTINCT  b.id_area as id, b.nama, b.kapasitas, B.tanggal, B.jumlah'))
            ->from(\DB::raw('(SELECT area_stok.id_area, area.nama, area.kapasitas, area_stok.tanggal, area_stok.jumlah FROM area_stok JOIN area ON area_stok.id_area = area.id WHERE area_stok.id_material = '.$id_material.' ORDER BY id_area ) AS b'))
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->groupBy(\DB::raw('b.id_area, b.nama, b.kapasitas, B.tanggal, B.jumlah'))
            ->orderBy(\DB::raw('nama'))
            ->get();
        } else {
            $resource = Area::select(
                'area.id',
                'area.nama',
                'area.kapasitas',
                \DB::raw('null as tanggal'),
                \DB::raw('null as jumlah')
            )
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })->get();
        }
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function pindahArea(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = Area::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getAreaStok($id_aktivitas, $id_material, $id_area)
    {
        $aktivitas = Aktivitas::findOrFail($id_aktivitas);
        if ($aktivitas->pengaruh_tgl_produksi != null) {
            $detail = \DB::table('')->selectRaw(
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
                $detail = $detail->orderBy('nama', 'ASC');
            } else {
                $detail = $detail->orderBy('tanggal', 'ASC');
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

    public function getAlatBerat(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = AlatBerat::
        leftJoin('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
        ->where('status', 1)
        ->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            $where->orWhere(\DB::raw('LOWER(nomor_lambung)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function store(ApiAktivitasRequest $req, AktivitasHarian $aktivitas)
    {
        $req->validated();

        $user = $req->get('my_auth');
        $res_user = Users::findOrFail($user->id_user);

        if ($res_user->role_id == 3) {
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

            $gudang = Gudang::find($rencana_tkbm->id_gudang)->orderBy('id', 'desc')->first();

            if (empty($gudang)) {
                $this->responseCode = 500;
                $this->responseMessage = 'Gudang tidak tersedia!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }

            //simpan aktivitas
            $aktivitas->id_aktivitas      = $req->input('id_aktivitas');
            $aktivitas->id_gudang         = $gudang->id;
            $aktivitas->id_karu           = $gudang->id_karu;
            $aktivitas->id_shift          = $rencana_tkbm->id_shift;
            $aktivitas->id_gudang_tujuan  = $req->input('id_gudang_tujuan');
            $aktivitas->ref_number        = $req->input('ref_number');
            $aktivitas->id_area           = $req->input('id_pindah_area');
            $aktivitas->id_alat_berat     = $req->input('id_alat_berat');
            $aktivitas->sistro            = $req->input('sistro');
            $aktivitas->approve           = $req->input('approve');
            $aktivitas->kelayakan_before  = $req->input('kelayakan_before');
            $aktivitas->kelayakan_after   = $req->input('kelayakan_after');
            $aktivitas->dikembalikan      = $req->input('dikembalikan');
            $aktivitas->alasan            = $req->input('alasan');
            $aktivitas->created_by        = $res_user->id;
            $aktivitas->created_at        = now();

            $saved = $aktivitas->save();

            if ($saved) {
                $res_aktivitas = Aktivitas::find($req->input('id_aktivitas'));

                //simpan produk
                if ($res_aktivitas->pengaruh_tgl_produksi != null) { //jika tidak pengaruh tanggal produksi dicentang
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
                                $id_area_stok = $list_area[$j]['id_area_stok'];
                                $list_jumlah = $list_area[$j]['list_jumlah'];
                                $jums_list_jumlah = count($list_jumlah);

                                for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                    if ($res_aktivitas->fifo != null) {
                                        $area_stok = AreaStok::where('id_area', $id_area_stok)
                                        ->where('id_material', $produk)
                                        ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                        ->orderBy('tanggal', 'asc')
                                        ->first();
                                    } else {
                                        $area_stok = AreaStok::where('id_area', $id_area_stok)
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
            
                                        $material_trans = new MaterialTrans;
                                        
                                        $array = [
                                            'id_material'           => $produk,
                                            'id_aktivitas_harian'   => $aktivitas->id,
                                            'tanggal'               => now(),
                                            'tipe'                  => $tipe,
                                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                                            'status_produk'         => $status_produk,
                                        ];

                                        $material_trans->create($array);
                                    } else {
                                        $this->responseCode = 500;
                                        $this->responseMessage = 'Gagal menyimpan aktivitas! Tidak ada area dengan produk yang cocok';
                                        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                        return response()->json($response, $this->responseCode);
                                    }
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
                                    $id_area_stok = $list_area[$j]['id_area_stok'];
                                    $list_jumlah = $list_area[$j]['list_jumlah'];
                                    $jums_list_jumlah = count($list_jumlah);
    
                                    for ($k = 0; $k < $jums_list_jumlah; $k++) {
                                        // $area_stok = AreaStok::where('id_area', $id_area_stok)
                                        //     ->where('id_material', $produk)
                                        //     ->where('tanggal', date('Y-m-d', strtotime($list_jumlah[$k]['tanggal'])))
                                        //     ->first();

                                        $area_stok = new AreaStok;
                                        
    
                                        $arr = [
                                            'id_material'   => $produk,
                                            'id_area'       => $id_area_stok,
                                            'jumlah'        => $list_jumlah[$k]['jumlah'],
                                            'tanggal'       => now(),
                                        ];
    
                                        $area_stok->create($arr);
    
                                        $material_trans = new MaterialTrans;

                                        $array = [
                                            'id_material'           => $produk,
                                            'id_aktivitas_harian'   => $aktivitas->id,
                                            'tanggal'               => now(),
                                            'tipe'                  => $tipe,
                                            'jumlah'                => $list_jumlah[$k]['jumlah'],
                                            'status_produk'         => $status_produk,
                                        ];
                                        $material_trans->create($array);
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
                            'id_aktivitas_harian'       => $aktivitas->id,
                            'tanggal'                   => now(),
                            'id_material'               => $pallet,
                            'jumlah'                    => $jumlah,
                            'tipe'                      => $tipe,
                            'status_pallet'             => $status_pallet,
                        ];

                        $materialTrans = new MaterialTrans;

                        $materialTrans->create($arr);

                        $gudangPallet = new GudangPallet;

                        $arr = [
                            'id_gudang' => $gudang->id,
                            'id_material' => $pallet,
                            'jumlah' => $jumlah,
                            'status_pallet' => $status_pallet,
                        ];
                        $simpan_pallet = $gudangPallet->create($arr);
                    }
                }


                return (new AktivitasResource($aktivitas))->additional([
                    'produk' => $list_produk,
                    'pallet' => $list_pallet,
                    'status' => [
                        'message' => '',
                        'code' => Response::HTTP_CREATED,
                    ]
                ], Response::HTTP_CREATED);
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'Gagal menyimpan aktivitas!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Hanya Checker yang diizinkan untuk menyimpan aktivitas!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
    }

    public function storePhotos(ApiSavePhotosRequest $req, AktivitasHarian $aktivitas) 
    {
        $req->validated();

        $user = $req->get('my_auth');

        $res_user = Users::findOrFail($user->id_user);

        $id_aktivitas_harian = $req->input('id_aktivitas_harian');
        $aktivitas = AktivitasHarian::find($id_aktivitas_harian);


        $ttd = $req->file('ttd');
        if (!empty($ttd)) {
            if ($ttd->isValid()) {
                \Storage::deleteDirectory('/public/aktivitas_harian/' . $id_aktivitas_harian);
                $ttd->storeAs('/public/aktivitas_harian/' . $id_aktivitas_harian, $ttd->getClientOriginalName());
                $aktivitas->ttd = $ttd->getClientOriginalName();
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
            \Storage::deleteDirectory('/public/aktivitas_harian/' . $id_aktivitas_harian);
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
                        'created_at'                => now(),
                    ];

                    $aktivitasFoto->create($arrayFoto);
                }
            }

            $foto = AktivitasFoto::where('id_aktivitas_harian', $id_aktivitas_harian)->get();
        }

        return (new AktivitasResource($foto))->additional([
            // 'foto' => $foto,
            'status' => [
                'message' => '',
                'code' => Response::HTTP_CREATED,
            ]
        ], Response::HTTP_CREATED);
    }

    public function storeKelayakanPhotos(ApiSaveKelayakanPhotos $req)
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
                \Storage::deleteDirectory('/public/kelayakan/' . $id_aktivitas_harian);
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
                            'created_at'                => now(),
                        ];

                        $aktivitasKelayakanFoto->create($arrayFoto);
                    }
                }

                $foto = AktivitasKelayakanFoto::where('id_aktivitas_harian', $id_aktivitas_harian)->get();
            }

            return (new AktivitasResource($foto))->additional([
                // 'foto' => $foto,
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

    public function approve(AktivitasHarian $aktivitas)
    {
        $aktivitas->approve = date('Y-m-d H:i:s');

        $saved = $aktivitas->save();

        if ($saved) {
            return (new AktivitasResource($aktivitas))->additional([
                'status' => [
                    'message' => 'Aktivitas Harian berhasil disetujui',
                    'code' => Response::HTTP_OK,
                ]
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'data' => null,
                'status' => [
                    'message' => 'Aktivitas Harian tidak dapat diapprove. Terjadi kesalahan dalam pemrosesan!',
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Aktivitas $aktivitas)
    {
        // $aktivitas = Aktivitas::findOrFail($id);
        return (new AktivitasResource($aktivitas))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
        // try {
        // } catch (ModelNotFoundException $ex) {
        //     return response()->json([
        //         'data' => null,
        //         'status' => [
        //             'message' => 'Data tidak ditemukan!',
        //             'code' => Response::HTTP_NOT_FOUND
        //         ]
        //     ], Response::HTTP_NOT_FOUND);
        // }
    }

    public function getJenisFoto(Request $req)
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

    public function getKelayakanFoto(Request $req)
    {
        $id_aktivitas_harian = $req->input('id_aktivitas_harian');
        $resource = AktivitasKelayakanFoto::select(
            'id',
            'jenis',
            \DB::raw('CASE WHEN jenis = 1 THEN \'Before Kelayakan\' ELSE \'After Kelayakan\' END AS text_jenis'),
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

    public function areaStok()
    {
        $area = Area::join('area_stok', 'area.id', '=', 'area_stok.id_area')->orderBy('area_stok.tanggal', 'asc')
            ->get();

        collect($area)->groupBy('tanggal');

        return $area->toArray();
    }

    public function history(Request $req)
    {
        $search = $req->input('search');

        $res = AktivitasHarian::select(
            'aktivitas_harian.id',
            'aktivitas.nama as nama_aktivitas',
            'gudang.nama as nama_gudang',
            \DB::raw('CASE WHEN approve IS NOT NULL THEN \'Done\' ELSE \'Progress\' END AS text_status'),
            'aktivitas_harian.created_at',
            'aktivitas_harian.created_by'
        )
        ->join('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
        ->join('gudang', 'aktivitas_harian.id_gudang', '=', 'gudang.id')
        ->where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(aktivitas.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            $where->orWhere(\DB::raw('LOWER(gudang.nama)'), 'ILIKE', '%' . strtolower($search) . '%');
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

    public function detailHistory($id)
    {
        $res = AktivitasHarian::select(
            'aktivitas_harian.id',
            'aktivitas.nama as nama_aktivitas',
            'gudang.nama as nama_gudang',
            'nomor_lambung',
            'sistro',
            'internal_gudang',
            'butuh_approval',
            \DB::raw('
                CASE
                    WHEN internal_gudang IS NOT NULL AND butuh_approval IS NOT NULL THEN true
                ELSE false
            END AS tombol_approval'),
            \DB::raw('
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
            \DB::raw('CASE WHEN approve IS NOT NULL THEN \'Done\' ELSE \'Progress\' END AS text_status'),
            'aktivitas_harian.created_at',
            'aktivitas_harian.created_by'
        )
        ->join('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
        ->join('gudang', 'aktivitas_harian.id_gudang', '=', 'gudang.id')
        ->join('alat_berat', 'aktivitas_harian.id_gudang', '=', 'alat_berat.id')
        ->where('aktivitas_harian.id', $id)
        ;

        $res_produk = MaterialTrans::select(
            'material.id as id_material',
            'material.nama as nama_material',
            'tipe',
            'status_produk',
            \DB::raw('CASE WHEN status_produk=1 THEN \'Produk Stok\' ELSE \'Produk Rusak\' END AS text_status_produk'),
            \DB::raw('CASE WHEN tipe=1 THEN \'Mengurangi\' ELSE \'Menambah\' END AS text_tipe'),
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
            \DB::raw('CASE WHEN status_pallet=1 THEN \'Pallet Stok\' WHEN status_pallet=2 THEN \'Pallet Dipakai\' WHEN status_pallet=3 THEN \'Pallet Kosong\' ELSE \'Pallet Rusak\' END AS text_status_pallet'),
            \DB::raw('CASE WHEN tipe=1 THEN \'Mengurangi\' ELSE \'Menambah\' END AS text_tipe'),
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

    public function historyMaterialArea($id_material)
    {
        $res = AreaStok::select(
            'id_area',
            'nama',
            'jumlah'
        )
        ->leftJoin('area', 'area.id', '=', 'area_stok.id_area')
        ->where('id_material', $id_material);

        $obj = (new AktivitasResource($res->get()))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }
}
