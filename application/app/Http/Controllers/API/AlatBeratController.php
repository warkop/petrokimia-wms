<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\AlatBerat;
use App\Http\Models\AlatBeratKerusakan;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\LaporanKerusakanFoto;
use App\Http\Models\RencanaTkbm;
use App\Http\Models\ShiftKerja;
use App\Http\Models\Users;
use App\Http\Requests\ApiAktivitasRequest;
use App\Http\Requests\ApiLaporanKerusakanRequest;
use App\Http\Resources\AktivitasResource;
use Illuminate\Http\Response;

class AlatBeratController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $res = AlatBerat::
            select(
                'alat_berat.id', 
                'nomor_lambung', 
                'nama', 
                'status',
                \DB::raw('
                    CASE
                        WHEN status=\'1\' THEN \'Aktif\'
                    ELSE \'Rusak\'
                END AS text_status'),
                'alat_berat.created_at'
            )
            ->join('alat_berat_kat as abk', 'alat_berat.id_kategori', '=', 'abk.id')
            ->join('rencana_alat_berat as rab', 'alat_berat.id', '=', 'rab.id_alat_berat')
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('LOWER(nomor_lambung)'), 'ILIKE', '%' . strtolower($search) . '%');
            })
            ->orderBy('alat_berat.created_at', 'asc')
            ->paginate(10);

        $obj =  AktivitasResource::collection($res)->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK
            ],
        ], Response::HTTP_OK);

        return $obj;
    }

    public function history(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $id_alat_berat = strip_tags($req->input('id_alat_berat'));

        $res = LaporanKerusakan::select(
            'laporan_kerusakan.id', 
            'id_kerusakan', 
            'id_alat_berat', 
            'id_shift',
            \DB::raw('CASE WHEN jenis=1 THEN \'Perbaikan\' ELSE \'Keluhan\' END AS jenis_pelaporan'),
            \DB::raw('TO_CHAR(jam_rusak, \'dd-mm-yyyy\') as tanggal'), 
            \DB::raw('TO_CHAR(jam_rusak, \'HH24:MI:SS\') as pukul'), 
            'keterangan')
            
            ->join('alat_berat_kerusakan as abk', 'laporan_kerusakan.id_kerusakan', '=', 'abk.id')
            ->join('alat_berat as ab', 'laporan_kerusakan.id_alat_berat', '=', 'ab.id')
            ->leftJoin('shift_kerja as s', 'laporan_kerusakan.id_shift', '=', 's.id')
            // ->where('rab.id_alat_berat', 'laporan_kerusakan.id_alat_berat')
            ->where(function ($where) use ($search) {
                $where->where(\DB::raw('LOWER(keterangan)'), 'ILIKE', '%' . strtolower($search) . '%');
                $where->orWhere(\DB::raw('TO_CHAR(jam_rusak, \'dd-mm-yyyy\')'), 'ILIKE', '%' . $search . '%');
                $where->orWhere(\DB::raw('TO_CHAR(jam_rusak, \'HH24:MI:SS\')'), 'ILIKE', '%' . $search . '%');
            })
            ->orderBy('laporan_kerusakan.created_at', 'desc')
            ;
        
        if (!empty($id_alat_berat)){
            $res = $res->where('laporan_kerusakan.id_alat_berat', $id_alat_berat);
        }
        
        if (!empty($res)) {
            $obj =  AktivitasResource::collection($res->paginate(10))->additional([
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK
                ],
            ], Response::HTTP_OK);
        } else {
            $obj = [
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_NOT_FOUND,
                ],
            ];
        }


        return $obj;
    }

    public function detailHistory($id)
    {
        $res = LaporanKerusakan::
        select(
            'laporan_kerusakan.id',
            'id_kerusakan',
            'id_alat_berat',
            'id_shift',
            's.nama as nama_shift',
            'jenis',
            \DB::raw('CASE WHEN jenis=1 THEN \'Perbaikan\' ELSE \'Keluhan\' END AS jenis_pelaporan'),
            'abk.nama as nama_kerusakan',
            \DB::raw('TO_CHAR(jam_rusak, \'dd-mm-yyyy\') as tanggal'),
            \DB::raw('TO_CHAR(jam_rusak, \'HH24:MI:SS\') as pukul'),
            'keterangan'
        )
        ->leftJoin('alat_berat_kerusakan as abk', 'laporan_kerusakan.id_kerusakan', '=', 'abk.id')
        ->leftJoin('alat_berat as ab', 'laporan_kerusakan.id_alat_berat', '=', 'ab.id')
        ->leftJoin('shift_kerja as s', 'laporan_kerusakan.id_shift', '=', 's.id')
        ->where('laporan_kerusakan.id', $id)->first();

        $foto = LaporanKerusakanFoto::where('id_laporan', $id)->get();

        if (!empty($res)) {
            $obj =  (new AktivitasResource($res))->additional([
                'foto' => $foto,
                'url' => '{{base_url}}/watch/{{file_ori}}?token={{token}}&un={{id_laporan}}&ctg=history&src={{file_enc}}',
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK
                ],
            ], Response::HTTP_OK);
        } else {
            $obj = [
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_NOT_FOUND,
                ],
            ];
        }

        return $obj;
    }

    public function store(ApiLaporanKerusakanRequest $req, LaporanKerusakan $laporan)
    {
        $req->validated();
        // $models = new LaporanKerusakan;

        $user = $req->get('my_auth');

        $res_user = Users::find($user->id_user);

        // dump($res_user);
        
        if ($res_user->role_id == 3) {
            $rencana_tkbm = RencanaTkbm::
            leftJoin('rencana_harian', 'id_rencana', '=', 'rencana_harian.id')
            ->where('id_tkbm', $user->id_tkbm)
            ->orderBy('rencana_harian.id', 'desc')
            ->take(1)->first();

            $laporan->id_kerusakan      = $req->input('id_kerusakan');
            $laporan->id_alat_berat     = $req->input('id_alat_berat');
            $laporan->id_shift          = $rencana_tkbm->id_shift;
            $laporan->keterangan        = $req->input('keterangan');
            $laporan->jenis             = $req->input('jenis');
            $laporan->jam_rusak         = $req->input('jam_rusak');
            $laporan->created_by        = $user->id_user;
            $laporan->created_at        = now();

            $resource = $laporan->save();

            if ($resource) {
                $foto = $req->file('foto');
                (new LaporanKerusakanFoto)->where('id_laporan', '=', $laporan->id)->delete();
                \Storage::deleteDirectory('/public/history/' . $laporan->id);
                if (!empty($foto)) {
                    foreach ($foto as $key => $value) {
                        if ($value->isValid()) {
                            $res = new LaporanKerusakanFoto;

                            $tujuan_upload = storage_path('app/public/history/') . $laporan->id;
                            $md5Name = md5_file($value->getRealPath());
                            $guessExtension = $value->getClientOriginalExtension();
                            // \Storage::makeDirectory('/history/' . $laporan->id);
                            $file = $value->storeAs('/public/history/' . $laporan->id, $md5Name . '.' . $guessExtension);

                            // $value->move($tujuan_upload, $value->getClientOriginalName());

                            $arrayFoto = [
                                'id_laporan'    => $laporan->id,
                                'file_ori'      => $value->getClientOriginalName(),
                                'size'          => $value->getSize(),
                                'ekstensi'      => $value->getClientOriginalExtension(),
                                'file_enc'      => $md5Name . '.' . $guessExtension,
                            ];

                            $res->create($arrayFoto);
                        }
                    }

                    $foto = LaporanKerusakanFoto::where('id_laporan', $laporan->id)->get();
                } else {
                    $foto = null;
                }


                return (new AktivitasResource($laporan))->additional([
                    'file' => $foto,
                    'status' => [
                        'message' => 'Data berhasil disimpan',
                        'code' => Response::HTTP_CREATED,
                    ]
                ], Response::HTTP_CREATED);
            } else {
                $this->responseCode = 500;
                $this->responseMessage = 'Gagal menyimpan laporan!';
                $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                return response()->json($response, $this->responseCode);
            }
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Hanya Checker yang diizinkan untuk menyimpan Laporan Kerusakan!';
            $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
            return response()->json($response, $this->responseCode);
        }
    }

    public function getKerusakan(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = AlatBeratKerusakan::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getShift(Request $req)
    {
        $search = strip_tags($req->input('search'));
        $resource = ShiftKerja::where(function ($where) use ($search) {
            $where->where(\DB::raw('LOWER(nama)'), 'ILIKE', '%' . strtolower($search) . '%');
        })->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }
}