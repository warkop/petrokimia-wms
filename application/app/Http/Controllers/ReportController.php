<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasHarian;
use App\Http\Resources\ReportAktivitasHarianResource;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function aktivitasHarian(Request $req)
    {
        $tgl_awal   = date('Y-m-d', strtotime($req->input('tgl_awal')));
        $tgl_akhir  = date('Y-m-d', strtotime($req->input('tgl_akhir')));

        $res = AktivitasHarian::with('aktivitas')->with('gudang')->with('checker')->with('produk')->whereBetween('created_at', [$tgl_awal, $tgl_akhir])->get();
        return ReportAktivitasHarianResource::collection($res);
    }
}
