<?php

use App\Http\Models\ShiftKerja;
use Illuminate\Database\Seeder;

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
            'akhir'         => '14:30',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('shift_kerja')->insert([
            'id'            => 2,
            'nama'          => 'Shift 2',
            'mulai'         => '14:30',
            'akhir'         => '22:00',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::table('shift_kerja')->insert([
            'id'            => 3,
            'nama'          => 'Shift 3',
            'mulai'         => '22:00',
            'akhir'         => '07:00',
            'start_date'    => date('Y-m-d', strtotime("01-01-2019")),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
