<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\KeluhanOperator;
use App\Http\Requests\KeluhanOperatorRequest;
use App\Http\Resources\KeluhanOperatorResource;

class KeluhanOperatorController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $obj =  KeluhanOperatorResource::collection(KeluhanOperator::where('keterangan', 'ILIKE', '%'.$search.'%')->paginate(10))->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);

        return $obj;
    }

    public function show(KeluhanOperator $keluhanOperator)
    {
        $obj =  (new KeluhanOperatorResource($keluhanOperator))->additional([
            'status' => [
                'message' => '',
                'code' => 200
            ],
        ], 200);

        return $obj;
    }

    public function store(KeluhanOperatorRequest $req, KeluhanOperator $keluhanOperator)
    {
        $req->validated();

        $user = $req->get('my_auth');

        try {
            $keluhanOperator->keterangan    = $req->input('keterangan');
            $keluhanOperator->id_operator   = $req->input('id_operator');
            $keluhanOperator->id_keluhan    = $req->input('id_keluhan');
            $keluhanOperator->created_by    = $user->id_user;
            $keluhanOperator->created_at    = now();

            $keluhanOperator->save();

            $obj = [
                'data' => $keluhanOperator,
                'status' => [
                    'message' => 'Data berhasil disimpan!',
                    'code' => 201
                ],
            ];
            
            return $obj;
        } catch (\Exception $e) {
            $data = [
                'error_message' => $e->getMessage(),
                'status' => [
                    'message' => 'Ada kesalahan saat menyimpan ke database!',
                    'code' => 500
                ],
            ];

            return response()->json($data, 500);
        }
        

        
    }
}
