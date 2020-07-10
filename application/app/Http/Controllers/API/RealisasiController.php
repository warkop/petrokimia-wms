<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Area;
use App\Http\Models\AreaHousekeeperFoto;
use App\Http\Models\FotoBuruh;
use App\Http\Models\Gudang;
use App\Http\Models\GudangStok;
use App\Http\Models\Karu;
use App\Http\Models\Material;
use App\Http\Models\MaterialTrans;
use App\Http\Models\Realisasi;
use App\Http\Models\RealisasiHousekeeper;
use App\Http\Models\RealisasiMaterial;
use App\Http\Models\RencanaAreaTkbm;
use App\Http\Models\RencanaHarian;
use App\Http\Models\Users;
use App\Http\Requests\ApiRealisasiRequest;
use App\Http\Requests\RealisasiMaterialRequest;
use App\Http\Resources\AktivitasResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RealisasiController extends Controller
{
    public function index()
    {
        $data = Realisasi::get();

        return AktivitasResource::collection($data)->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);
    }

    public function show($id)
    {
        $realisasi = Realisasi::where('id_rencana', $id)->firstOrFail();

        $realisasiHousekeeper = RealisasiHousekeeper::select(
            'realisasi_housekeeper.id',
            'realisasi_housekeeper.id_realisasi',
            'realisasi_housekeeper.id_tkbm',
            'realisasi_housekeeper.id_area',
            'tk.nama as nama_housekeeper',
            'area.nama as nama_area'
        )
        ->join('tenaga_kerja_non_organik as tk', 'tk.id', '=', 'realisasi_housekeeper.id_tkbm')
        ->join('area', 'area.id', '=', 'realisasi_housekeeper.id_area')
        ->where('id_realisasi', $realisasi->id)->with('areaHousekeeperFoto')->get();

        $fotoBuruh = FotoBuruh::where('id_realisasi', $realisasi->id)->get();
        
        $res = collect($realisasi);
        $res = $res->merge([
            'list_housekeeper' => $realisasiHousekeeper, 
            'foto_buruh' => $fotoBuruh, 
            'url' => '{{base_url}}/watch/{{foto}}?token={{token}}&un={{id_realisasi_housekeeper}}&ctg=realisasi_housekeeper&src={{file_enc}}',
            'url_foto_buruh' => '{{base_url}}/watch/{{foto}}?token={{token}}&un={{id_realisasi}}&ctg=foto_buruh&src={{file_enc}}',
        ]);


        return (new AktivitasResource($res))->additional([
            'status' => [
                'message'   => '',
                'code'      => 200,
            ]
        ], 200);
    }

    public function getMaterial(Request $req)
    {
        $user = $req->get('my_auth');
        $users = Users::findOrFail($user->id_user);
        $karu = Karu::findOrFail($users->id_karu);
        $gudang = Gudang::findOrFail($karu->id_gudang);

        $res = Material::select(
            'material.id',
            'id_material_sap',
            'id_plant',
            'nama'
        )
        // ->join('gudang_stok as gs', 'gs.id_material', '=', 'material.id')
        ->lainlain()
        // ->where('id_gudang', $gudang->id)
        ->get();

        return (new AktivitasResource($res))->additional([
            'status' => [
                'message'   => '',
                'code'      => 200,
            ]
        ], 200);
    }

    public function getArea(Request $req, $id_gudang = '')
    {
        $user = $req->get('my_auth');

        $users = Users::findOrFail($user->id_user);
        if ($id_gudang == '') {
            $karu = Karu::find($users->id_karu);
            $gudang = Gudang::find($karu->id_gudang);
            if (!empty($gudang)) {
                $res = Area::where('id_gudang', $gudang->id)->get();

                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia';
                $this->responseData = $res;
            } else {
                $this->responseCode = 403;
                $this->responseMessage = 'Anda tidak memiliki gudang! Silahkan daftarkan gudang Anda pada menu Gudang!';
            }
        } else {
            $res = Area::where('id_gudang', $id_gudang)->get();

            $this->responseCode = 200;
            $this->responseMessage = 'Data tersedia';
            $this->responseData = $res;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getHousekeeper($id_rencana)
    {
        if (is_numeric($id_rencana)) {
            $this->responseData = RencanaAreaTkbm::select(
                'id_tkbm', 
                'id_area', 
                'area.nama as nama_area',
                'tenaga_kerja_non_organik.nama as nama_tkbm'
            )
            ->where('id_rencana', $id_rencana)
            ->join('area', 'id_area', '=', 'area.id')
            ->join('tenaga_kerja_non_organik', 'id_tkbm', '=', 'tenaga_kerja_non_organik.id')
            ->orderBy('tenaga_kerja_non_organik.nama', 'asc')
            ->get();
            $this->responseCode = 200;
        } else {
            $this->responseMessage = 'ID rencana tidak ditemukan';
            $this->responseCode = 400;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function store(ApiRealisasiRequest $req, Realisasi $realisasi)
    {
        $req->validated();

        $id_rencana     = $req->input('id_rencana');
        $jumlah_buruh   = $req->input('jumlah_buruh');
        $foto_buruh     = $req->foto_buruh;
        $user           = $req->get('my_auth');
        $rencana        = RencanaHarian::findOrFail($id_rencana);

        $temp_res = (new Realisasi)->where('id_rencana', $id_rencana)->first();
        (new Realisasi)->where('id_rencana', $id_rencana)->forceDelete();

        if (!empty($temp_res)) {
            $realisasiHousekeeper = RealisasiHousekeeper::where('id_realisasi', $temp_res->id)->get();

            foreach ($realisasiHousekeeper as $key) {
                (new AreaHousekeeperFoto)->where('id_realisasi_housekeeper', $key->id)->forceDelete();
                Storage::deleteDirectory('/public/realisasi_housekeeper/' . $key->id);
            }
            (new RealisasiHousekeeper)->where('id_realisasi', $temp_res->id)->forceDelete();
            (new FotoBuruh)->where('id_realisasi', $key->id)->forceDelete();
            Storage::deleteDirectory('/public/foto_buruh/' . $key->id);
        }

        $housekeeper    = $req->input('housekeeper');
        $housekeeper    = array_values((array)$housekeeper);

        $realisasi->id_rencana  = $id_rencana;
        $realisasi->jumlah_buruh  = $jumlah_buruh;
        $realisasi->tanggal     = now();
        $realisasi->created_at  = now();
        $realisasi->created_by  = $user->id_user;
        $realisasi->updated_by  = $user->id_user;
        $realisasi->save();
        if (!empty($housekeeper)) {
            foreach ($housekeeper as $key => $value) {
                $temp = array_values((array)$req->input('area_housekeeper')[$key]);
                if (!empty($temp)) {
                    foreach ($temp as $row => $hey) {
                        if (isset($key,$req->foto[$key])) {
                            if (isset($req->foto[$key][$row])) {
                                $foto = $req->foto[$key][$row];
                            } else {
                                $foto = '';
                            }
                        } else {
                            $foto = '';
                        }
                        
                        $arr = [
                            'id_realisasi'  => $realisasi->id,
                            'id_tkbm'       => $value,
                            'id_area'       => $hey,
                        ];

                        $realisasi_housekeeper = (new RealisasiHousekeeper)->create($arr);
                        if (!empty($foto)) {
                            $panjang = count($foto);
                            
                            for ($i = 0; $i < $panjang; $i++) {
                                if ($foto[$i]->isValid()) {
                                    $areaHousekeeperFoto = new AreaHousekeeperFoto();
                                    // dd($foto[$i]);
                                    storage_path('app/public/realisasi_housekeeper/') . $realisasi_housekeeper->id;
                                    $md5Name = md5_file($foto[$i]->getRealPath());
                                    $guessExtension = $foto[$i]->getClientOriginalExtension();
                                    $foto[$i]->storeAs('/public/realisasi_housekeeper/' . $realisasi_housekeeper->id, $md5Name . '.' . $guessExtension);
                                    $arrayFoto = [
                                        'id_realisasi_housekeeper'  => $realisasi_housekeeper->id,
                                        'foto'                      => $foto[$i]->getClientOriginalName(),
                                        'size'                      => $foto[$i]->getSize(),
                                        'ekstensi'                  => $foto[$i]->getClientOriginalExtension(),
                                        'file_enc'                  => $md5Name . '.' . $guessExtension,
                                        'created_by'                => $user->id_user,
                                        'created_at'                => now(),
                                    ];

                                    $areaHousekeeperFoto->create($arrayFoto);
                                }
                            }
                        }
                    }
                }
            }
        }

        // dd($foto_buruh);
        if (!empty($foto_buruh)) {
            $panjang = count($foto_buruh);
            for ($i = 0; $i < $panjang; $i++) {
                $fotoBuruh = new FotoBuruh();

                // storage_path('app/public/foto_buruh/') . $realisasi->id;
                // dd($foto_buruh[$i]->getRealPath());
                $md5Name = md5_file($foto_buruh[$i]->getRealPath());
                $guessExtension = $foto_buruh[$i]->getClientOriginalExtension();
                $foto_buruh[$i]->storeAs('/public/foto_buruh/' . $realisasi->id, $md5Name . '.' . $guessExtension);
                $arrayFoto = [
                    'id_realisasi'              => $realisasi->id,
                    'foto'                      => $foto_buruh[$i]->getClientOriginalName(),
                    'size'                      => $foto_buruh[$i]->getSize(),
                    'ekstensi'                  => $foto_buruh[$i]->getClientOriginalExtension(),
                    'file_enc'                  => $md5Name . '.' . $guessExtension,
                    'created_by'                => $user->id_user,
                    'updated_by'                => $user->id_user,
                ];

                $fotoBuruh->create($arrayFoto);
            }
        }        

        $housekeeper = RealisasiHousekeeper::with('areaHousekeeperFoto')->where('id_realisasi', $realisasi->id)->get();
        $fotoBuruh = FotoBuruh::where('id_realisasi', $realisasi->id)->get();

        $this->responseData = ['realisasi' => $realisasi, 'housekeeper' => $housekeeper, 'foto_buruh' => $fotoBuruh];
        $this->responseCode = 200; 

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getRealisasiMaterial()
    {
        $search = request()->input('search');
        $res = RealisasiMaterial::select(
            'realisasi_material.id',
            'tanggal',
            'nama'
        )
        ->leftJoin('shift_kerja', 'realisasi_material.id_shift', '=', 'shift_kerja.id')
        ->where(function($query) use ($search){
            $query->where(DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
            $query->orWhere(DB::raw('TO_CHAR(tanggal, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
        })
        ->orderBy('realisasi_material.created_at', 'desc')
        ->paginate(10);
        return AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);
    }

    public function getShowRealisasiMaterial(RealisasiMaterial $realisasiMaterial)
    {
        $detail = MaterialTrans::select(
            'material_trans.id',
            'id_material',
            'tanggal',
            'nama',
            'tipe',
            'jumlah',
            'alasan',
            'id_realisasi_material'
        )
        ->leftJoin('material', 'material.id', '=', 'material_trans.id_material')
        ->where('id_realisasi_material', $realisasiMaterial->id)->get();

        $res = collect($realisasiMaterial);
        $res = $res->merge(['detail' => $detail]);


        return (new AktivitasResource($res))->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);
    }

    public function storeMaterial(RealisasiMaterialRequest $req, RealisasiMaterial $realisasiMaterial)
    {
        $req->validate();
        $user = $req->get('my_auth');
        $karu = Karu::find($user->id_karu);
        $rencana_harian = RencanaHarian::where('id_gudang', $karu->id_gudang)->orderBy('id', 'desc')->first();

        if (empty($rencana_harian)) {
            return response()->json([
                'status' => [
                    'message'   => 'Rencana tidak ditemukan',
                    'code'      => 403,
                ]
            ], 403);
        }

        $list_material   = $req->input('list_material');
        
        $tipe       = $req->input('tipe');
        $jumlah     = $req->input('jumlah');

        if (count($list_material) < 1) {
            $this->responseMessage = 'Harus minimal satu material yang diinputkan!';
            $this->responseCode = 403;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }

        $realisasiMaterial->tanggal       = now();
        $realisasiMaterial->id_shift      = $rencana_harian->id_shift;
        $realisasiMaterial->created_at    = now();
        $realisasiMaterial->created_by    = $user->id_user;

        $realisasiMaterial->save();
        if (!empty($list_material)) {
            $panjang    = count($list_material);
            for ($i = 0; $i < $panjang; $i++) {
                $material   = $list_material[$i]['material'];
                $tipe   = $list_material[$i]['tipe'];
                $jumlah   = $list_material[$i]['jumlah'];

                $gudangStok = GudangStok::where('id_gudang', $karu->id_gudang)->where('id_material', $material)->first();
                if (empty($gudangStok)) {
                    $gudangStok = new GudangStok;
                    $gudangStok->jumlah         = $jumlah;
                } else {
                    if ($tipe == 1) {
                        $gudangStok->jumlah         = $gudangStok->jumlah - $jumlah;
                    } else if ($tipe == 2) {
                        $gudangStok->jumlah         = $gudangStok->jumlah + $jumlah;
                    }
                }

                $gudangStok->id_gudang      = $karu->id_gudang;
                $gudangStok->id_material    = $material;
                $gudangStok->status         = 1;
                $gudangStok->save();

                $arr = [
                    'id_realisasi_material' => $realisasiMaterial->id,
                    'id_gudang_stok'        => $gudangStok->id,
                    'id_material'           => $material,
                    'tanggal'               => now(),
                    'tipe'                  => $tipe,
                    'jumlah'                => $jumlah,
                ];

                (new MaterialTrans)->create($arr);

            }
        }

        $data = MaterialTrans::where('id_realisasi_material', $realisasiMaterial->id)->get();

        $array = [
            'id'            => $realisasiMaterial->id,
            'created_at'    => date('Y-m-d H:i:s', strtotime($realisasiMaterial->created_at)),
            'tanggal'       => date('Y-m-d', strtotime($realisasiMaterial->tanggal)),
            'id_shift'      => $realisasiMaterial->id_shift,
        ];

        $this->responseData = ['realisasi_material' => $array, 'data' => $data];
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
