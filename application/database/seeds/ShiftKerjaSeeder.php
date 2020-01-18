<?php

use App\Http\Models\ShiftKerja;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShiftKerja::truncate();

        DB::table('shift_kerja')->insert([
            'id'            => 1,
            'nama'          => 'Shift 1',
            'mulai'         => '07:00',
            'akhir'         => '15:00',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('shift_kerja')->insert([
            'id'            => 2,
            'nama'          => 'Shift 2',
            'mulai'         => '15:00',
            'akhir'         => '23:00',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('shift_kerja')->insert([
            'id'            => 3,
            'nama'          => 'Shift 3',
            'mulai'         => '23:00',
            'akhir'         => '07:00',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
