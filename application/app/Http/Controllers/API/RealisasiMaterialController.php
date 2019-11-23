<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Realisasi;
use App\Http\Resources\AktivitasResource;

class RealisasiMaterialController extends Controller
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

    public function store()
    {
        
    }
}
