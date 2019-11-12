<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\AktivitasArea;
use App\Http\Models\AktivitasFoto;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\AlatBerat;
use App\Http\Models\Area;
use App\Http\Models\AreaStok;
use App\Http\Models\Gudang;
use App\Http\Models\JenisFoto;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Requests\ApiAktivitasRequest;
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

    public function getArea(Request $req, $id_aktivitas)
    {
        $search = strip_tags($req->input('search'));
        $aktivitas = Aktivitas::find($id_aktivitas);
        if ($aktivitas->pengaruh_tgl_produksi != null) {
            $resource = \DB::table('')->select(\DB::raw('DISTINCT  b.id_area, b.nama, b.kapasitas'))
            ->from(\DB::raw('(SELECT area_stok.id_area, area.nama, area.kapasitas, area_stok.tanggal, area_stok.jumlah FROM area_stok LEFT JOIN area ON area_stok.id_area = area.id ORDER BY id_area ) AS b'))
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->groupBy(\DB::raw('b.id_area, b.nama, b.kapasitas'))
            ->orderBy(\DB::raw('nama'))
            ->get();
        } else {
            $resource = Area::select(
                'area.id',
                'area.nama',
                'area.kapasitas'
                // \DB::raw('null as tanggal'),
                // \DB::raw('null as jumlah')
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

    public function getAreaStok($id_aktivitas, $id_area)
    {
        $aktivitas = Aktivitas::find($id_aktivitas);
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
            ->leftJoin('area', 'area.id', '=', 'area_stok.id_area')
            ->where('area.id', $id_area)
            ->orderBy('nama', 'ASC')->get();
            return (new AktivitasResource($detail))->additional([
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

        $res_user = Users::find($user->id_user);

        if ($res_user == 3) {
            $ttd = $req->file('ttd');
            if (!empty($ttd)) {
                if ($ttd->isValid()) {
                    \Storage::deleteDirectory('/public/aktivitas_harian/' . $aktivitas->id);
                    $ttd->storeAs('/public/aktivitas_harian/' . $aktivitas->id, $ttd->getClientOriginalName());
                    $aktivitas->ttd = $ttd->getClientOriginalName();
                }
            }

            $aktivitas->id_aktivitas      = $req->input('id_aktivitas');
            $aktivitas->id_gudang         = $req->input('id_gudang');
            $aktivitas->id_karu           = $req->input('id_karu');
            $aktivitas->id_shift          = $req->input('id_shift');
            $aktivitas->ref_number        = $req->input('ref_number');
            $aktivitas->id_area           = $req->input('id_area');
            $aktivitas->id_alat_berat     = $req->input('id_alat_berat');
            $aktivitas->sistro            = $req->input('sistro');
            $aktivitas->approve           = $req->input('approve');
            $aktivitas->kelayakan_before  = $req->input('kelayakan_before');
            $aktivitas->kelayakan_after   = $req->input('kelayakan_after');
            $aktivitas->dikembalikan      = $req->input('dikembalikan');
            $aktivitas->created_by        = $user->id_user;
            $aktivitas->created_at        = now();

            $saved = $aktivitas->save();

            if ($saved) {
                //simpan foto
                $foto = $req->file('foto');
                $foto_jenis = $req->input('foto_jenis');
                $lat = $req->input('lat');
                $lng = $req->input('lng');
                if (!empty($foto)) {
                    $panjang = count($foto);
                    (new AktivitasFoto)->where('id_aktivitas_harian', '=', $aktivitas->id)->delete();
                    \Storage::deleteDirectory('/public/aktivitas_harian/' . $aktivitas->id);
                    for ($i = 0; $i < $panjang; $i++) {
                        if ($foto[$i]->isValid()) {
                            $aktivitasFoto = new AktivitasFoto;

                            $foto[$i]->storeAs('/public/aktivitas_harian/' . $aktivitas->id, $foto[$i]->getClientOriginalName());

                            $arrayFoto = [
                                'id_aktivitas_harian'       => $aktivitas->id,
                                'id_foto_jenis'             => $foto_jenis[$i],
                                'foto'                      => $foto[$i]->getClientOriginalName(),
                                'size'                      => $foto[$i]->getSize(),
                                'lat'                       => $lat[$i],
                                'lng'                       => $lng[$i],
                                'created_by'                => $user->id_user,
                                'created_at'                => now(),
                            ];

                            $aktivitasFoto->create($arrayFoto);
                        }
                    }

                    $foto = AktivitasFoto::where('id_aktivitas_harian', $aktivitas->id)->get();
                }


                //simpan area
                $area_stok = $req->input('area_stok');
                $jumlah = $req->input('jumlah');
                $tipe = $req->input('tipe');
                if (!empty($area_stok)) {
                    $panjang = count($area_stok);
                    (new AktivitasArea)->where('id_aktivitas_harian', '=', $aktivitas->id)->delete();
                    for ($i = 0; $i < $panjang; $i++) {
                        $aktivitas_area = AktivitasArea::sum('jumlah')->where('id_aktivitas', $aktivitas->id)->get();

                        $area = Area::join('area_stok', 'area.id', '=', 'area_stok.id_area')->orderBy('area_stok.tanggal', 'asc')
                            ->get();

                        $area->groupBy();

                        $arr = [
                            'id_aktivitas_harian'       => $aktivitas->id,
                            'id_area_stok'              => $area_stok[$i],
                            'jumlah'                    => $jumlah[$i],
                            'tipe'                      => $tipe[$i],
                            'created_by'                => $user->id_user,
                            'created_at'                => now(),
                        ];

                        (new AktivitasArea)->create($arr);
                    }
                }

                //simpan produk
                $produk = $req->input('produk');
                $jumlah = $req->input('jumlah');
                $tipe = $req->input('tipe');
                if (!empty($produk)) {
                    $panjang = count($produk);
                    (new MaterialTrans)->where('id_aktivitas_harian', '=', $aktivitas->id)->delete();
                    for ($i = 0; $i < $panjang; $i++) {
                        $arr = [
                            'id_aktivitas_harian'       => $aktivitas->id,
                            'id_material'               => $produk[$i],
                            'jumlah'                    => $jumlah[$i],
                            'tipe'                      => $tipe[$i],
                            'created_by'                => $user->id_user,
                            'created_at'                => now(),
                        ];
                        \DB::table('material_trans')->insert($arr);
                    }
                }

                //simpan pallet
                $pallet = $req->input('pallet');
                $jumlah = $req->input('jumlah');
                $tipe = $req->input('tipe');
                if (!empty($pallet)) {
                    $panjang = count($pallet);
                    for ($i = 0; $i < $panjang; $i++) {
                        $arr = [
                            'id_aktivitas_harian'       => $aktivitas->id,
                            'id_material'               => $pallet[$i],
                            'jumlah'                    => $jumlah[$i],
                            'tipe'                      => $tipe[$i],
                            'created_by'                => $user->id_user,
                            'created_at'                => now(),
                        ];
                        \DB::table('material_trans')->insert($arr);
                    }
                }


                return (new AktivitasResource($aktivitas))->additional([
                    'foto' => $foto,
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
            $this->responseMessage = 'Hanya Checker yang diizinkan untuk menyimpan aktivitass!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
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

    public function show($id)
    {
        try {
            $aktivitas = Aktivitas::findOrFail($id);
            return (new AktivitasResource($aktivitas))->additional([
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK,
                ]
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'data' => null,
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_NOT_FOUND
                ]
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function getJenisFoto()
    {
        $resource = JenisFoto::get();
        return (new AktivitasResource($resource))->additional([
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
        ->leftjoin('aktivitas', 'aktivitas.id', '=', 'aktivitas_harian.id_aktivitas')
        ->leftjoin('gudang', 'aktivitas_harian.id_gudang', '=', 'gudang.id')
        ->leftjoin('alat_berat', 'aktivitas_harian.id_gudang', '=', 'alat_berat.id')
        ->where('aktivitas_harian.id', $id)
        ;

        $res_produk = MaterialTrans::select(
            'material.id as id_material',
            'material.nama as nama_material',
            'tipe',
            \DB::raw('CASE WHEN tipe=1 THEN \'Mengurangi\' ELSE \'Menambah\' END AS text_tipe'),
            'jumlah'
        )
        ->leftJoin('material', 'material_trans.id_material', '=', 'material.id')
        ->where('id_aktivitas_harian', $id)
        ->where('kategori', 1)
        ->get();

        $res_pallet = MaterialTrans::select(
            'material.id as id_material',
            'material.nama as nama_material',
            'tipe',
            \DB::raw('CASE WHEN tipe=1 THEN \'Mengurangi\' ELSE \'Menambah\' END AS text_tipe'),
            'jumlah'
        )
        ->leftJoin('material', 'material_trans.id_material', '=', 'material.id')
        ->where('id_aktivitas_harian', $id)
        ->where('kategori', 2)
        ->get();

        $foto = AktivitasFoto::where('id_aktivitas_harian', $id)->get();

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
