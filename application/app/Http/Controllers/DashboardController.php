<?php

namespace App\Http\Controllers;

use App\Http\Models\AktivitasKeluhanGp;
use App\Http\Models\LaporanKerusakan;
use App\Http\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        // $this->authorize('dashboard');
        $data['title'] = 'Dashboard';
        $laporan_kerusakan = [];

        $shift = ShiftKerja::get()->count();
        $shift1 = [];
        $shift2 = [];
        $shift3 = [];
        $komplain_gp_shift1 = [];
        $komplain_gp_shift2 = [];
        $komplain_gp_shift3 = [];

        for ($i=1; $i <= $shift; $i++) {
            for ($j=0; $j < 7; $j++) { 
                $temp = LaporanKerusakan::selectRaw('
                (SELECT COUNT(*) FROM laporan_kerusakan as s1 WHERE s1.jenis = 2 and id_shift='.$i.' and EXTRACT(DOW from s1.created_at)='.$j.') AS shift')
                ->distinct()
                ->first();
                
                if ($temp)
                    array_push(${'shift'.$i}, $temp);
            }
        }
        $data['shift1'] = $shift1;
        $data['shift2'] = $shift2;
        $data['shift3'] = $shift3;

        for ($i = 1; $i <= $shift; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $temp = AktivitasKeluhanGp::selectRaw('
                    (SELECT 
                        COUNT(*) 
                    FROM aktivitas_keluhan_gp as s1 
                    JOIN aktivitas_harian as ah ON ah.id = s1.id_aktivitas_harian 
                    WHERE draft = 0 and id_shift=' . $i . ' and EXTRACT(DOW from s1.created_at)=' . $j . ') AS shift')
                    ->distinct()
                    ->first();

                if ($temp)
                    array_push(${'komplain_gp_shift' . $i}, $temp);
            }
        }

        $data['komplain_gp_shift1'] = $komplain_gp_shift1;
        $data['komplain_gp_shift2'] = $komplain_gp_shift2;
        $data['komplain_gp_shift3'] = $komplain_gp_shift3;

        


        return view('dashboard.grid', $data);
    }
}