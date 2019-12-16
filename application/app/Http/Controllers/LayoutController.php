<?php

namespace App\Http\Controllers;

use App\Http\Models\Area;
use App\Http\Resources\LayoutAreaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LayoutController extends Controller
{
    public function index()
    {
        $area = Area::whereNotNull('koordinat')->get();

        // $data['area'] = LayoutAreaResource::collection($area);
        return view('menu-layout.grid');
    }

    public function loadArea()
    {
        $area = Area::select(
            '*',
            \DB::raw('(select sum(jumlah) from area_stok where area_stok.id_area = area.id) AS terpakai')
        )->whereNotNull('koordinat')->get();

        $this->responseData = LayoutAreaResource::collection($area);
        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function detailArea($id)
    {
        $area = Area::with('areaStok', 'areaStok.material')->with('gudang')->find($id);
        $this->responseData = $area;
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
