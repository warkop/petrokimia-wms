<?php

namespace App\Http\Controllers;

use App\Http\Models\LaporanKerusakan;
use App\Http\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $data['title'] = 'Dashboard';
        $laporan_kerusakan = [];

        $shift = ShiftKerja::get()->count();
        $shift1 = [];
        $shift2 = [];
        $shift3 = [];

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

        // dd($shift1);
        // dd($shift2);
        // dd($shift3);
        return view('dashboard.grid', $data);
    }
}