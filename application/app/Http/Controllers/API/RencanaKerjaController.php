<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\RencanaHarian;
use App\Http\Models\Users;
use App\Http\Requests\ApiRencanaKerjaRequest;
use Illuminate\Http\Response;

class RencanaKerjaController extends Controller
{
    public function index()
    {
        $data = RencanaHarian::get();

        return response()->json(['data' => $data,
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);
    }

    public function store(ApiRencanaKerjaRequest $req, RencanaHarian $rencanaHarian)
    {
        $req->validated();

        $user = $req->get('my_auth');

        $res_user = Users::find($user->id_user);

        if ($res_user->role_id == 5) {
        }
    }
}
